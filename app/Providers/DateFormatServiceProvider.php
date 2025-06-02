<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DateFormatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('date.formatter', function($app){
            return new class {
                public function formatDateInBengali($date)
                {
                    $bengaliMonths = [
                        1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল', 5 => 'মে', 6 => 'জুন',
                        7 => 'জুলাই', 8 => 'অগাস্ট', 9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর'
                    ];
                    $bengaliDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

                    $timestamp = strtotime($date);
                    $day = date('j', $timestamp);
                    $month = date('n', $timestamp);
                    $year = date('Y', $timestamp);

                    $day = str_replace(range(0, 9), $bengaliDigits, $day);
                    $year = str_replace(range(0, 9), $bengaliDigits, $year);

                    return $day . ' ' . $bengaliMonths[$month] . ', ' . $year;
                }
            };
        });
        $this->app->singleton('date.formatter2', function($app){
            return new class {
                public function toBanglaDigit($englishDateTime) {
                    // Mapping of English digits to Bengali digits
                    $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                    $bengaliDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        
                    // Replace English digits with Bengali digits
                    $bengaliDateTime = str_replace($englishDigits, $bengaliDigits, $englishDateTime);
                    return $bengaliDateTime;
                }
            };
        });
    }
    public function boot()
    {

    }
}
