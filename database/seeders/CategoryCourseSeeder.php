<?php

namespace Database\Seeders;

use App\Enums\BookingRequestStatus;
use App\Models\BookingRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseRun;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryCourseSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * ✅ تفريغ الجداول (FK safe)
         */
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BookingRequest::truncate();
        CourseRun::truncate();
        Course::truncate();
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /**
         * ✅ مسارات الصور الافتراضية للـ Seeder
         */
        $categoryCoverPath = database_path('seeders/assets/categories/cover.jpg');
        $courseCoverPath   = database_path('seeders/assets/courses/cover.jpg');
        $courseGalleryDir  = database_path('seeders/assets/courses/gallery');

        $galleryImages = [];
        if (is_dir($courseGalleryDir)) {
            $galleryImages = glob($courseGalleryDir . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
        }

        // 10 Categories
        for ($i = 1; $i <= 10; $i++) {
            $category = Category::create([
                'name' => [
                    'ar' => "فئة رقم {$i}",
                    'en' => "Category {$i}",
                ],
                'description' => [
                    'ar' => "وصف بسيط للفئة رقم {$i}",
                    'en' => "Simple description for category {$i}",
                ],
                'sort_order' => $i,
                'is_active' => (bool) random_int(0, 1),
            ]);

            /**
             * ✅ Category cover (Spatie) - نفس الصورة لكل الكاتيجوريز
             */
            if (is_file($categoryCoverPath)) {
                $category
                    ->addMedia($categoryCoverPath)
                    ->preservingOriginal()
                    ->toMediaCollection(Category::MEDIA_COLLECTION_COVER);
            }

            // 5 Courses لكل Category
            for ($j = 1; $j <= 5; $j++) {
                $course = Course::create([
                    'category_id' => $category->id,
                    'title' => [
                        'ar' => "كورس {$j} ضمن الفئة {$i}",
                        'en' => "Course {$j} in Category {$i}",
                    ],
                    'duration_hours' => rand(10, 40),
                    'description' => [
                        'ar' => "وصف بسيط للكورس {$j} ضمن الفئة {$i}",
                        'en' => "Simple description for course {$j} in category {$i}",
                    ],
                    'is_active' => (bool) random_int(0, 1),
                    'is_featured' => (bool) random_int(0, 1),
                ]);

                /**
                 * ✅ Course cover (Spatie) - نفس الصورة لكل الكورسات
                 */
                if (is_file($courseCoverPath)) {
                    $course
                        ->addMedia($courseCoverPath)
                        ->preservingOriginal()
                        ->toMediaCollection(Course::MEDIA_COLLECTION_COVER);
                }

                /**
                 * ✅ Course gallery (Spatie) - اختار 2-4 صور عشوائي من فولدر الـ gallery
                 */
                if (!empty($galleryImages)) {
                    $count = random_int(2, min(4, count($galleryImages)));
                    $picked = collect($galleryImages)->shuffle()->take($count)->values();

                    foreach ($picked as $imgPath) {
                        if (is_file($imgPath)) {
                            $course
                                ->addMedia($imgPath)
                                ->preservingOriginal()
                                ->toMediaCollection(Course::MEDIA_COLLECTION_GALLERY);
                        }
                    }
                }

                // لكل كورس: نعمل 1-3 Runs
                $runsCount = random_int(1, 3);

                for ($r = 1; $r <= $runsCount; $r++) {
                    $startsAt = now()
                        ->addDays(random_int(1, 90))
                        ->setTime(random_int(8, 18), [0, 30][array_rand([0, 1])]);

                    CourseRun::create([
                        'course_id' => $course->id,
                        'starts_at' => now(),
                        'ends_at' => now()->addDays(random_int(1,7)),
                        'capacity' => random_int(10, 50),
                        'price' => random_int(0, 1) ? random_int(20, 150) : 0,
                        'status' => fake()->randomElement(['open', 'closed', 'cancelled', 'draft']),
                        'is_active' => (bool) random_int(0, 1),
                    ]);
                }
            }
        }

        // run IDs لأن booking_requests مربوط عليها
        $runIds = CourseRun::query()->pluck('id')->all();

        // 150 Booking Requests
        for ($k = 1; $k <= 150; $k++) {
            BookingRequest::create([
                'course_run_id' => $runIds[array_rand($runIds)],

                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),

                'phone' => fake()->optional()->phoneNumber(),
                'email' => fake()->optional()->safeEmail(),
                'address' => fake()->optional()->address(),

                'note' => fake()->optional()->text(),

                'meta' => [
                    'seeded' => true,
                    'source' => fake()->randomElement(['instagram', 'website', 'whatsapp', 'referral']),
                ],

                'status' => fake()->randomElement([
                    BookingRequestStatus::New->value,
                    BookingRequestStatus::Contacted->value,
                    BookingRequestStatus::Confirmed->value,
                    BookingRequestStatus::Rejected->value,
                ]),
            ]);
        }
    }
}
