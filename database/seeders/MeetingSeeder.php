<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Meeting;
use Illuminate\Database\Seeder;

/**
 * MeetingSeeder — recurring weekly H&I meetings for each facility.
 *
 * Uses the recurring-pattern fields added by
 * 2024_01_04_100000_update_meetings_for_recurring_patterns.php:
 *   day_of_week   : 0=Sun 1=Mon 2=Tue 3=Wed 4=Thu 5=Fri 6=Sat
 *   week_of_month : null = every week, 1-4 = specific week
 *   meeting_time  : time string (HH:MM:SS)
 *   duration_minutes: always 60 unless noted
 *   scheduled_time  : null (pattern-based, not one-off)
 *
 * Gender notes (where a co-ed facility has gender-specific sessions)
 * are recorded in the 'notes' field. The meetings table has no
 * gender column — the facility-level gender_restriction flag governs
 * volunteer eligibility at the matching stage.
 *
 * ⚠️  ASSUMPTION: Glenwood Behavioral Health has no day specified →
 *      defaulted to Wednesday. Brookside Health Center has no day
 *      specified → defaulted to Thursday. Update if incorrect.
 */
class MeetingSeeder extends Seeder
{
    // Day-of-week constants (Carbon convention)
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
    // Sunday 5:30 pm MEN ONLY, Monday 6 pm co-ed,
    // Tuesday 7 pm MEN ONLY, Tuesday 1:30 pm Detox (co-ed)
    private function seedCAT(): void
    {
        $f = Facility::where('facility_name', 'Center for Addiction Treatment')->first();
        if (!$f) return;

        $meetings = [
            [
                'day_of_week'  => self::SUN,
                'meeting_time' => '17:30:00',
                'notes'        => 'Men only',
            ],
            [
                'day_of_week'  => self::MON,
                'meeting_time' => '18:00:00',
                'notes'        => null,
            ],
            [
                'day_of_week'  => self::TUE,
                'meeting_time' => '19:00:00',
                'notes'        => 'Men only',
            ],
            [
                'day_of_week'  => self::TUE,
                'meeting_time' => '13:30:00',
                'notes'        => 'Detox meeting',
            ],
        ];

        foreach ($meetings as $m) {
            Meeting::create(array_merge($this->defaults($f->facility_id), $m));
        }
    }

    // ── Glenwood Behavioral Health Hospital ───────────────────────────────
    // 7 pm — day not specified; defaulted to Wednesday ⚠️
    private function seedGlenwood(): void
    {
        $f = Facility::where('facility_name', 'Glenwood Behavioral Health Hospital')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'day_of_week'  => self::WED, // ⚠️ assumed — update if incorrect
            'meeting_time' => '19:00:00',
        ]));
    }

    // ── Lumiere Healing Center ────────────────────────────────────────────
    // Saturday 7 pm, Sunday 7 pm
    private function seedLumiere(): void
    {
        $f = Facility::where('facility_name', 'Lumiere Healing Center')->first();
        if (!$f) return;

        foreach ([self::SAT, self::SUN] as $day) {
            Meeting::create(array_merge($this->defaults($f->facility_id), [
                'day_of_week'  => $day,
                'meeting_time' => '19:00:00',
            ]));
        }
    }

    // ── Talbert House ADAPT ───────────────────────────────────────────────
    // Tuesday 7 pm
    private function seedTalbertAdapt(): void
    {
        $f = Facility::where('facility_name', 'Talbert House ADAPT')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'day_of_week'  => self::TUE,
            'meeting_time' => '19:00:00',
        ]));
    }

    // ── Brookside Health Center ───────────────────────────────────────────
    // 6 pm — day not specified; defaulted to Thursday ⚠️
    private function seedBrookside(): void
    {
        $f = Facility::where('facility_name', 'Brookside Health Center')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'day_of_week'  => self::THU, // ⚠️ assumed — update if incorrect
            'meeting_time' => '18:00:00',
        ]));
    }

    // ── Joseph House ──────────────────────────────────────────────────────
    // Friday 5:30 pm
    private function seedJosephHouse(): void
    {
        $f = Facility::where('facility_name', 'Joseph House')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'day_of_week'  => self::FRI,
            'meeting_time' => '17:30:00',
        ]));
    }

    // ── Barron Center ─────────────────────────────────────────────────────
    // Thursday 7 pm
    private function seedBarronCenter(): void
    {
        $f = Facility::where('facility_name', 'Barron Center')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'day_of_week'  => self::THU,
            'meeting_time' => '19:00:00',
        ]));
    }

    // ── Adams Recovery Center ─────────────────────────────────────────────
    // Saturday 7 pm
    private function seedAdamsRecovery(): void
    {
        $f = Facility::where('facility_name', 'Adams Recovery Center')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'day_of_week'  => self::SAT,
            'meeting_time' => '19:00:00',
        ]));
    }

    // ── Talbert House Pathways ────────────────────────────────────────────
    // Saturday 11 am
    private function seedTalbertPathways(): void
    {
        $f = Facility::where('facility_name', 'Talbert House Pathways')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'day_of_week'  => self::SAT,
            'meeting_time' => '11:00:00',
        ]));
    }

    // ── River City Correctional Center ────────────────────────────────────
    // Saturday 7 pm (men only — reflected in facility gender_restriction)
    private function seedRiverCity(): void
    {
        $f = Facility::where('facility_name', 'River City Correctional Center')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'day_of_week'  => self::SAT,
            'meeting_time' => '19:00:00',
            'notes'        => 'Men only (correctional facility)',
        ]));
    }

    // ── Esther Marie Hatton Center for Women ─────────────────────────────
    // Tuesday 2 pm (women only — reflected in facility gender_restriction)
    private function seedHattonCenter(): void
    {
        $f = Facility::where('facility_name', 'Esther Marie Hatton Center for Women')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'day_of_week'  => self::TUE,
            'meeting_time' => '14:00:00',
            'notes'        => 'Women only',
        ]));
    }

    // ── Crossroads Center ─────────────────────────────────────────────────
    // Monday 7 pm
    private function seedCrossroads(): void
    {
        $f = Facility::where('facility_name', 'Crossroads Center')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'day_of_week'  => self::MON,
            'meeting_time' => '19:00:00',
        ]));
    }

    // ── Talbert House Spring Grove Center ─────────────────────────────────
    // Friday 7 pm
    private function seedTalbertSpringGrove(): void
    {
        $f = Facility::where('facility_name', 'Talbert House Spring Grove Center')->first();
        if (!$f) return;

        Meeting::create(array_merge($this->defaults($f->facility_id), [
            'day_of_week'  => self::FRI,
            'meeting_time' => '19:00:00',
        ]));
    }

    // ── Shared defaults for every meeting ────────────────────────────────

    private function defaults(string $facilityId): array
    {
        return [
            'facility_id'      => $facilityId,
            'week_of_month'    => null,    // null = every week of the month
            'duration_minutes' => 60,
            'format'           => 'in_person',
            'volunteers_needed'=> 2,
            'status'           => 'scheduled',
            'notes'            => null,
            'scheduled_time'   => null,    // recurring pattern — no fixed date
        ];
    }
}
