<?php

namespace Database\Seeders;

use App\Enums\BookingRequestStatus;
use App\Models\BookingRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseRun;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryCourseSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * بما إنك لسا ببداية المشروع:
         * منفضّي الجداول بترتيب صح + نوقف القيود مؤقتاً لتفادي مشاكل FK.
         */
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BookingRequest::truncate();
        CourseRun::truncate();
        Course::truncate();
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 10 Categories
        for ($i = 1; $i <= 10; $i++) {
            $category = Category::create([
                'name' => [
                    'ar' => "فئة رقم {$i}",
                    'en' => "Category {$i}",
                ],
                // إذا عندك description بالكاتيجوري
                'description' => [
                    'ar' => "وصف بسيط للفئة رقم {$i}",
                    'en' => "Simple description for category {$i}",
                ],
                'slug' => Str::slug("category-{$i}"),
                // إذا عندك sort_order بالكاتيجوري
                'sort_order' => $i,
                'is_active' => (bool) random_int(0, 1),
            ]);

            // 5 Courses لكل Category
            for ($j = 1; $j <= 5; $j++) {
                $course = Course::create([
                    'category_id' => $category->id,
                    'title' => [
                        'ar' => "كورس {$j} ضمن الفئة {$i}",
                        'en' => "Course {$j} in Category {$i}",
                    ],
                    'duration_hours'=>rand(10,40),
                    'description' => [
                        'ar' => "وصف بسيط للكورس {$j} ضمن الفئة {$i}",
                        'en' => "Simple description for course {$j} in category {$i}",
                    ],
                    'is_active' => (bool) random_int(0, 1),
                ]);

                // لكل كورس: نعمل 1-3 دفعات (Runs)
                $runsCount = random_int(1, 3);

                for ($r = 1; $r <= $runsCount; $r++) {
                    $startsAt = now()->addDays(random_int(1, 90))->setTime(random_int(8, 18), [0, 30][array_rand([0, 1])]);

                    CourseRun::create([
                        'course_id' => $course->id,
                        'starts_at' => $startsAt,
                        'ends_at' => now()->addMinutes(2),
                        'capacity' => random_int(10, 50),
                        'price' => random_int(0, 1) ? random_int(20, 150) : 0,
                        'status' => fake()->randomElement(['open', 'closed', 'cancelled', 'draft']),
                        'is_active' => (bool) random_int(0, 1),
                    ]);
                }
            }
        }

        // نجيب IDs تبع الـ runs (بدل courses) لأن booking_requests مربوط عليها
        $runIds = CourseRun::query()->pluck('id')->all();

        // 30 Booking Requests (عدّل الرقم مثل ما بدك)
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
