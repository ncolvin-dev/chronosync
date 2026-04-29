<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Meeting;
use Illuminate\Database\Seeder;

/**
 * MeetingSeeder — recurring H&I meeting slots for each facility.
 *
 * Uses the recurring-pattern fields:
 *   day_of_week   : 0=Sun 1=Mon 2=Tue 3=Wed 4=Thu 5=Fri 6=Sat
 *   week_of_month : 1=1st  2=2nd  3=3rd  4=4th  5=Last
 *   meeting_time  : time string (HH:MM:SS)
 *   duration_minutes: 60 unless noted
 *   scheduled_time  : null (pattern-based, not one-off)
 *
 * Patterns used in this seed:
 *   • CAT                 — five separate slots (weeks 1–4 + Last)
 *   • Lumiere             — 1st & 3rd Saturday
 *   • Crossroads          — 1st & 3rd Monday
 *   • Brookside           — 2nd & 4th Thursday
 *   • Hatton Center       — 2nd & 4th Tuesday (women only)
 *   • Adams Recovery      — single meeting per month (1st Saturday only)
 *   • All others          — one slot each, varied weeks
 *
 * Gender notes are in 'notes'; gender_restriction on the Facility record
 * governs volunteer eligibility at the matching stage.
 *
 * ⚠️  ASSUMPTION: Glenwood Behavioral Health has no day on file →
 *      defaulted to Wednesday. Update if the actual day is known.
 */
class MeetingSeeder extends Seeder
{
    // Day-of-week constants (Carbon: 0 = Sunday, 6 = Saturday)
    const SUN = 0;
    const MON = 1;
    const TUE = 2;
    const WED = 3;
    const THU = 4;
    const FRI = 5;
    const SAT = 6;

    public function run(): void
    {
        $this->seedCAT();
        $this->seedGlenwood();
        $this->seedLumiere();
        $this->seedTalbertAdapt();
        $this->seedBrookside();
        $this->seedJosephHouse();
        $this->seedBarronCenter();
        $this->seedAdamsRecovery();
        $this->seedTalbertPathways();
        $this->seedRiverCity();
        $this->seedHattonCenter();
        $this->seedCrossroads();
        $this->seedTalbertSpringGrove();
    }

    // ── Center for Addiction Treatment ────────────────────────────────────
    // Five distinct slots covering all five week-of-month values.
    //   1st Sunday  5:30 PM — Men only
    //   2nd Monday  6:00 PM — Co-ed
    //   3rd Tuesday 7:00 PM — Men only
    //   4th Wednesday 1:30 PM — Detox (co-ed)
    //   Last Thursday 7:00 PM — Co-ed
    private function seedCAT(): void
    {
        $f = Facility::where('facility_name', 'Center for Addiction Treatment')->first();
        if (!$f) return;

        $meetings = [
            ['week_of_month' => 1, 'day_of_week' => self::SUN, 'meeting_time' => '17:30:00', 'notes' => 'Men only'],
            ['week_of_month' => 2, 'day_of_week' => self::MON, 'meeting_time' => '18:00:00', 'notes' => null],
            ['week_of_month' => 3, 'day_of_week' => self::TUE, 'meeting_time' => '19:00:00', 'notes' => 'Men only'],
            ['week_of_month' => 4, 'day_of_week' => self::WED, 'meeting_time' => '13:30:00', 'notes' => 'Detox meeting'],
            ['week_of_month' => 5, 'day_of_week' => self::THU, 'meeting_time' => '19:00:00', 'notes' => null],
        ];

        foreach ($meetings as $m) {
            Meeting::create(array_merge($this->defaults($f->facility_id), $m));
        }
    }

    // ── Glenwood Behavioral Health Hospital ───────────────────────────────
    // 2nd Wednesday 7:00 PM — day assumed ⚠️
    private function seedGlenwood(): void
    {
        $f = Facility::where('facility_name', 'Glenwood Behavioral Health Hospital')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'week_of_month' => 2,
            'day_of_week'   => self::WED, // ⚠️ assumed — update if incorrect
            'meeting_time'  => '19:00:00',
        ]));
    }

    // ── Lumiere Healing Center ────────────────────────────────────────────
    // 1st & 3rd Saturday 7:00 PM
    private function seedLumiere(): void
    {
        $f = Facility::where('facility_name', 'Lumiere Healing Center')->first();
        if (!$f) return;

        foreach ([1, 3] as $week) {
            Meeting::create(array_merge($this->defaults($f->facility_id), [
                'week_of_month' => $week,
                'day_of_week'   => self::SAT,
                'meeting_time'  => '19:00:00',
            ]));
        }
    }

    // ── Talbert House ADAPT ───────────────────────────────────────────────
    // 3rd Tuesday 7:00 PM
    private function seedTalbertAdapt(): void
    {
        $f = Facility::where('facility_name', 'Talbert House ADAPT')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'week_of_month' => 3,
            'day_of_week'   => self::TUE,
            'meeting_time'  => '19:00:00',
        ]));
    }

    // ── Brookside Health Center ───────────────────────────────────────────
    // 2nd & 4th Thursday 6:00 PM — day assumed ⚠️
    private function seedBrookside(): void
    {
        $f = Facility::where('facility_name', 'Brookside Health Center')->first();
        if (!$f) return;

        foreach ([2, 4] as $week) {
            Meeting::create(array_merge($this->defaults($f->facility_id), [
                'week_of_month' => $week,
                'day_of_week'   => self::THU, // ⚠️ assumed — update if incorrect
                'meeting_time'  => '18:00:00',
            ]));
        }
    }

    // ── Joseph House ──────────────────────────────────────────────────────
    // 4th Friday 5:30 PM
    private function seedJosephHouse(): void
    {
        $f = Facility::where('facility_name', 'Joseph House')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'week_of_month' => 4,
            'day_of_week'   => self::FRI,
            'meeting_time'  => '17:30:00',
        ]));
    }

    // ── Barron Center ─────────────────────────────────────────────────────
    // Last Thursday 7:00 PM
    private function seedBarronCenter(): void
    {
        $f = Facility::where('facility_name', 'Barron Center')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'week_of_month' => 5,
            'day_of_week'   => self::THU,
            'meeting_time'  => '19:00:00',
        ]));
    }

    // ── Adams Recovery Center ─────────────────────────────────────────────
    // 1st Saturday 7:00 PM — one meeting per month only
    private function seedAdamsRecovery(): void
    {
        $f = Facility::where('facility_name', 'Adams Recovery Center')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'week_of_month' => 1,
            'day_of_week'   => self::SAT,
            'meeting_time'  => '19:00:00',
        ]));
    }

    // ── Talbert House Pathways ────────────────────────────────────────────
    // Last Saturday 11:00 AM
    private function seedTalbertPathways(): void
    {
        $f = Facility::where('facility_name', 'Talbert House Pathways')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'week_of_month' => 5,
            'day_of_week'   => self::SAT,
            'meeting_time'  => '11:00:00',
        ]));
    }

    // ── River City Correctional Center ────────────────────────────────────
    // 3rd Saturday 7:00 PM — men only (reflected in facility gender_restriction)
    private function seedRiverCity(): void
    {
        $f = Facility::where('facility_name', 'River City Correctional Center')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'week_of_month' => 3,
            'day_of_week'   => self::SAT,
            'meeting_time'  => '19:00:00',
            'notes'         => 'Men only (correctional facility)',
        ]));
    }

    // ── Esther Marie Hatton Center for Women ─────────────────────────────
    // 2nd & 4th Tuesday 2:00 PM — women only (reflected in facility gender_restriction)
    private function seedHattonCenter(): void
    {
        $f = Facility::where('facility_name', 'Esther Marie Hatton Center for Women')->first();
        if (!$f) return;

        foreach ([2, 4] as $week) {
            Meeting::create(array_merge($this->defaults($f->facility_id), [
                'week_of_month' => $week,
                'day_of_week'   => self::TUE,
                'meeting_time'  => '14:00:00',
                'notes'         => 'Women only',
            ]));
        }
    }

    // ── Crossroads Center ─────────────────────────────────────────────────
    // 1st & 3rd Monday 7:00 PM
    private function seedCrossroads(): void
    {
        $f = Facility::where('facility_name', 'Crossroads Center')->first();
        if (!$f) return;

        foreach ([1, 3] as $week) {
            Meeting::create(array_merge($this->defaults($f->facility_id), [
                'week_of_month' => $week,
                'day_of_week'   => self::MON,
                'meeting_time'  => '19:00:00',
            ]));
        }
    }

    // ── Talbert House Spring Grove Center ─────────────────────────────────
    // Last Friday 7:00 PM
    private function seedTalbertSpringGrove(): void
    {
        $f = Facility::where('facility_name', 'Talbert House Spring Grove Center')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'week_of_month' => 5,
            'day_of_week'   => self::FRI,
            'meeting_time'  => '19:00:00',
        ]));
    }

    // ── Shared defaults for every recurring meeting ───────────────────────
    private function defaults(string $facilityId): array
    {
        return [
            'facility_id'       => $facilityId,
            'scheduled_time'    => null,  // recurring pattern — no fixed date
            'duration_minutes'  => 60,
            'format'            => 'in_person',
            'volunteers_needed' => 2,
            'status'            => 'active',
            'notes'             => null,
        ];
    }
}
