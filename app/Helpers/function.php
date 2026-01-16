<?php

if (!function_exists('formatRupiah')) {
    /**
     * Format number to Rupiah currency
     */
    function formatRupiah($amount, $withPrefix = true)
    {
        $formatted = number_format($amount, 0, ',', '.');
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('getTanggalMerah')) {
    /**
     * Get list of Indonesian national holidays for a given year
     */
    function getTanggalMerah($year = null)
    {
        $year = $year ?? date('Y');

        // This is a basic list, you should update this annually or use an API
        $holidays = [
            // 2025
            '2025-01-01' => 'Tahun Baru 2025',
            '2025-03-29' => 'Hari Raya Nyepi',
            '2025-03-30' => 'Wafat Isa Almasih',
            '2025-03-31' => 'Isra Miraj',
            '2025-04-18' => 'Cuti Bersama Idul Fitri',
            '2025-04-19' => 'Cuti Bersama Idul Fitri',
            '2025-04-20' => 'Idul Fitri 1446 H',
            '2025-04-21' => 'Idul Fitri 1446 H',
            '2025-04-22' => 'Cuti Bersama Idul Fitri',
            '2025-04-23' => 'Cuti Bersama Idul Fitri',
            '2025-05-01' => 'Hari Buruh Internasional',
            '2025-05-29' => 'Kenaikan Isa Almasih',
            '2025-06-01' => 'Hari Lahir Pancasila',
            '2025-06-27' => 'Idul Adha 1446 H',
            '2025-07-18' => 'Tahun Baru Islam 1447 H',
            '2025-08-17' => 'Hari Kemerdekaan RI',
            '2025-09-26' => 'Maulid Nabi Muhammad SAW',
            '2025-12-25' => 'Hari Raya Natal',

            // 2026
            '2026-01-01' => 'Tahun Baru 2026',
            // Add more as needed
        ];

        return array_filter($holidays, function($date) use ($year) {
            return strpos($date, $year) === 0;
        }, ARRAY_FILTER_USE_KEY);
    }
}

if (!function_exists('isTanggalMerah')) {
    /**
     * Check if a date is a national holiday
     */
    function isTanggalMerah($date)
    {
        $date = \Carbon\Carbon::parse($date)->format('Y-m-d');
        $holidays = getTanggalMerah(date('Y', strtotime($date)));

        return array_key_exists($date, $holidays);
    }
}

if (!function_exists('getPeriodePenggajian')) {
    /**
     * Get payroll period dates (bi-weekly)
     * Returns array of start and end dates
     */
    function getPeriodePenggajian($referenceDate = null)
    {
        $referenceDate = $referenceDate ? \Carbon\Carbon::parse($referenceDate) : \Carbon\Carbon::now();

        // Find the most recent Saturday (end of period)
        $endDate = $referenceDate->copy();
        while (!$endDate->isSaturday()) {
            $endDate->subDay();
        }

        // Start date is 2 weeks before (next day after previous period end)
        $startDate = $endDate->copy()->subWeeks(2)->addDay();

        return [
            'start' => $startDate,
            'end' => $endDate,
        ];
    }
}

if (!function_exists('hitungHariKerja')) {
    /**
     * Calculate working days between two dates (excluding Sundays)
     */
    function hitungHariKerja($startDate, $endDate)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $days = 0;

        while ($start->lte($end)) {
            if (!$start->isSunday()) {
                $days++;
            }
            $start->addDay();
        }

        return $days;
    }
}

if (!function_exists('formatJam')) {
    /**
     * Format time in H:i format
     */
    function formatJam($time)
    {
        if (!$time) return '-';

        return \Carbon\Carbon::parse($time)->format('H:i');
    }
}

if (!function_exists('formatTanggal')) {
    /**
     * Format date in Indonesian format
     */
    function formatTanggal($date, $format = 'd/m/Y')
    {
        if (!$date) return '-';

        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('formatTanggalIndo')) {
    /**
     * Format date in Indonesian long format
     */
    function formatTanggalIndo($date)
    {
        if (!$date) return '-';

        $date = \Carbon\Carbon::parse($date);
        $date->locale('id');

        return $date->isoFormat('D MMMM YYYY');
    }
}

if (!function_exists('getHariIndo')) {
    /**
     * Get Indonesian day name
     */
    function getHariIndo($date)
    {
        $date = \Carbon\Carbon::parse($date);
        $date->locale('id');

        return $date->dayName;
    }
}

if (!function_exists('hitungSelisihJam')) {
    /**
     * Calculate hour difference between two times
     */
    function hitungSelisihJam($timeStart, $timeEnd)
    {
        if (!$timeStart || !$timeEnd) return 0;

        $start = \Carbon\Carbon::parse($timeStart);
        $end = \Carbon\Carbon::parse($timeEnd);

        return $start->diffInHours($end, true);
    }
}
