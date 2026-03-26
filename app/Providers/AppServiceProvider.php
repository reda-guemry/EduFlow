<?php

namespace App\Providers;

use App\Repositories\CoursePurchaseRepository;
use App\Repositories\CourseRepository;
use App\Repositories\Interfaces\CoursePurchaseRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\EnrollmentRepository;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\FavoriteRepository;
use App\Repositories\Interfaces\FavoriteRepositoryInterface;
use App\Repositories\GroupRepository;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);
        $this->app->bind(FavoriteRepositoryInterface::class, FavoriteRepository::class);
        $this->app->bind(EnrollmentRepositoryInterface::class, EnrollmentRepository::class);
        $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class); 
        $this->app->bind(CoursePurchaseRepositoryInterface::class, CoursePurchaseRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
