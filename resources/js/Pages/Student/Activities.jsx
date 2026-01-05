import StudentLayout from "@/Layouts/StudentLayout";
import { Head, Link } from "@inertiajs/react";

export default function Activities({ class: studentClass, activities }) {
  return (
    <StudentLayout>
      <Head title="My Activities" />

      <div className="min-h-screen bg-[#cfe9c8] px-4 py-8">
        <div className="w-full max-w-7xl mx-auto">

          {/* Top Bar */}
          <div className="flex items-center justify-between mb-6">
            <Link
              href={route("student.activities.categories")}
              className="
                inline-flex items-center
                gap-2
                bg-white
                border border-[#3f5f1f]
                text-[#3f5f1f]
                font-semibold
                px-4 py-2
                rounded-full
                shadow
                hover:bg-[#eef6ea]
                transition
              "
            >
              ← Back to Categories
            </Link>

            <span className="hidden sm:block text-sm text-green-900 font-medium">
              Select an activity to begin
            </span>
          </div>

          {/* Header Card */}
          <div className="bg-[#3f5f1f] rounded-3xl px-6 py-5 mb-8 shadow-lg">
            <h1 className="text-2xl sm:text-3xl font-extrabold text-[#cfe9c8] text-center">
              {studentClass?.name}
            </h1>
            <p className="text-center text-sm sm:text-base text-[#e6f3df] mt-1">
              Your Activities
            </p>
          </div>

          {/* Activities Grid */}
          {activities.length === 0 ? (
            <div className="text-center text-gray-700 font-medium">
              No activities available yet 🌱
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {activities.map((activity) => (
                <div
                  key={activity.id}
                  className="
                    bg-white
                    rounded-3xl
                    shadow-xl
                    p-4 sm:p-6 md:p-8
                    flex flex-col
                    min-h-[420px]
                  "
                >
                  {activity.image_path && (
                    <img
                      src={`/${activity.image_path}`}
                      alt={activity.title}
                      className="
                        rounded-xl
                        mb-4
                        h-56 sm:h-64
                        w-full
                        object-cover
                      "
                    />
                  )}

                  <div className="flex-1">
                    <h2 className="text-lg sm:text-xl font-bold text-green-900 mb-2">
                      {activity.title}
                    </h2>
                  </div>

                  <Link
                    href={route("student.activity.show", activity.id)}
                    className="
                      mt-4
                      bg-[#3f5f1f]
                      hover:bg-[#2f4816]
                      text-white
                      text-center
                      py-2 sm:py-3
                      rounded-full
                      font-semibold
                      text-base sm:text-lg
                      shadow-md
                      transition
                    "
                  >
                    {activity.category === "reference"
                      ? "View Reference"
                      : "Start Activity"}
                  </Link>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </StudentLayout>
  );
}
