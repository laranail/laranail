<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Macros\Supports\Holidays;

use Illuminate\Support\Carbon;

trait MultiNationalDates
{
    public function registerMultinationalDates()
    {
        Carbon::macro('isNewYearsDay', function () {
            return $this->month === 1 && $this->day === 1;
        });

        Carbon::macro('isEasterSunday', function () {
            return $this->clone()
                ->setMonth(3)
                ->setDay(21)
                ->eq($this->clone()->subDays(easter_days($this->year)));
        });

        Carbon::macro('isGoodFriday', function () {
            return $this->clone()
                ->addDays(2)
                ->isEasterSunday();
        });

        Carbon::macro('isAllSaintsDay', function () {
            // All Saints' Day is a Christian solemnity celebrated in honour of all the saints, known and unknown.
            // Its intent is to celebrate all the saints, including those who do not celebrated individually.
            // https://en.wikipedia.org/wiki/All_Saints%27_Day

            return $this->month === 11 && $this->day === 1;
        });

        Carbon::macro('isChristmasDay', function () {
            return $this->month === 12 && $this->day === 25;
        });

        Carbon::macro('isNewYearsEve', function () {
            return $this->month === 12 && $this->day === 31;
        });
    }
}