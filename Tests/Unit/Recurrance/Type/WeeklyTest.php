<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\Recurrance\Type;

use Tx\CzSimpleCal\Recurrance\Timeline\Base;
use Tx\CzSimpleCal\Recurrance\Type\Weekly;
use Tx\CzSimpleCal\Tests\Unit\Recurrance\Type\Mocks\IsRecurringMock;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * testing the features of Tx_CzSimpleCal_Recurrance_Type_Weekly
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class WeeklyTest extends UnitTestCase
{
    public function testRecurranceSubtype2Week()
    {
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-02-13 23:31:30GMT',
                'end' => '2009-02-13 23:31:30GMT',
                'recurrance_until' => '2009-03-04 16:00:00GMT',
                'recurrance_subtype' => '2week',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(2, count($return->toArray()), 'exactly two events returned');
        self::assertEquals(
            [
                'start' => strtotime('2009-02-13 23:31:30GMT'),
                'end' => strtotime('2009-02-13 23:31:30GMT'),
            ],
            $return->current(),
            'first event equals the input settings'
        );
        self::assertEquals(
            [
                'start' => strtotime('2009-02-27 23:31:30GMT'),
                'end' => strtotime('2009-02-27 23:31:30GMT'),
            ],
            $return->next(),
            'correct interval used'
        );
    }

    public function testRecurranceSubtype3Week()
    {
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-02-13 23:31:30GMT',
                'end' => '2009-02-13 23:31:30GMT',
                'recurrance_until' => '2009-03-11 16:00:00GMT',
                'recurrance_subtype' => '3week',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(2, count($return->toArray()), 'exactly two events returned');
        self::assertEquals(
            [
                'start' => strtotime('2009-02-13 23:31:30GMT'),
                'end' => strtotime('2009-02-13 23:31:30GMT'),
            ],
            $return->current(),
            'first event equals the input settings'
        );
        self::assertEquals(
            [
                'start' => strtotime('2009-03-06 23:31:30GMT'),
                'end' => strtotime('2009-03-06 23:31:30GMT'),
            ],
            $return->next(),
            'correct interval used'
        );
    }

    public function testRecurranceSubtype4Week()
    {
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-02-13 23:31:30GMT',
                'end' => '2009-02-13 23:31:30GMT',
                'recurrance_until' => '2009-03-18 16:00:00GMT',
                'recurrance_subtype' => '4week',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(2, count($return->toArray()), 'exactly two events returned');
        self::assertEquals(
            [
                'start' => strtotime('2009-02-13 23:31:30GMT'),
                'end' => strtotime('2009-02-13 23:31:30GMT'),
            ],
            $return->current(),
            'first event equals the input settings'
        );
        self::assertEquals(
            [
                'start' => strtotime('2009-03-13 23:31:30GMT'),
                'end' => strtotime('2009-03-13 23:31:30GMT'),
            ],
            $return->next(),
            'correct interval used'
        );
    }

    public function testRecurranceSubtypeOddEven()
    {
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-02-13 23:31:30GMT',
                'end' => '2009-02-13 23:31:30GMT',
                'recurrance_until' => '2009-03-04 16:00:00GMT',
                'recurrance_subtype' => 'oddeven',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(2, count($return->toArray()), 'exactly two events returned');
        self::assertEquals(
            [
                'start' => strtotime('2009-02-13 23:31:30GMT'),
                'end' => strtotime('2009-02-13 23:31:30GMT'),
            ],
            $return->current(),
            'first event equals the input settings'
        );
        self::assertEquals(
            [
                'start' => strtotime('2009-02-27 23:31:30GMT'),
                'end' => strtotime('2009-02-27 23:31:30GMT'),
            ],
            $return->next(),
            'correct interval used'
        );

        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-12-28 23:31:30GMT',
                'end' => '2009-12-28 23:31:30GMT',
                'recurrance_until' => '2010-01-07 16:00:00GMT',
                'recurrance_subtype' => 'oddeven',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(2, count($return->toArray()), 'on year switch: exactly two events returned');
        self::assertEquals(
            [
                'start' => strtotime('2009-12-28 23:31:30GMT'),
                'end' => strtotime('2009-12-28 23:31:30GMT'),
            ],
            $return->current(),
            'on year switch: first event equals the input settings'
        );
        self::assertEquals(
            [
                'start' => strtotime('2010-01-04 23:31:30GMT'),
                'end' => strtotime('2010-01-04 23:31:30GMT'),
            ],
            $return->next(),
            'on year switch: correct interval used'
        );
    }

    public function testRecurranceSubtypeWeekly()
    {
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-02-13 23:31:30GMT',
                'end' => '2009-02-13 23:31:30GMT',
                'recurrance_until' => '2009-02-25 16:00:00GMT',
                'recurrance_subtype' => 'weekly',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(2, count($return->toArray()), 'exactly two events returned');
        self::assertEquals(
            [
                'start' => strtotime('2009-02-13 23:31:30GMT'),
                'end' => strtotime('2009-02-13 23:31:30GMT'),
            ],
            $return->current(),
            'first event equals the input settings'
        );
        self::assertEquals(
            [
                'start' => strtotime('2009-02-20 23:31:30GMT'),
                'end' => strtotime('2009-02-20 23:31:30GMT'),
            ],
            $return->next(),
            'times are preserved'
        );
    }

    public function testRecurranceUntil()
    {
        $event = IsRecurringMock::fromArray(
            [
                'start' => '2009-02-13 23:31:30GMT',
                'end' => '2009-02-13 23:31:30GMT',
                'recurrance_until' => '2009-02-20 23:59:59GMT',
            ]
        );

        $return = $this->buildRecurrance($event);

        self::assertEquals(2, count($return->toArray()), 'exactly two events returned');
        self::assertEquals(
            [
                'start' => strtotime('2009-02-13 23:31:30GMT'),
                'end' => strtotime('2009-02-13 23:31:30GMT'),
            ],
            $return->current(),
            'first event equals the input settings'
        );
        self::assertEquals(
            [
                'start' => strtotime('2009-02-20 23:31:30GMT'),
                'end' => strtotime('2009-02-20 23:31:30GMT'),
            ],
            $return->next(),
            'times are preserved'
        );
    }

    protected function buildRecurrance($event, $timeline = null)
    {
        if (is_null($timeline)) {
            $timeline = new Base();
        }
        $typeWeekly = new Weekly();

        return $typeWeekly->build($event, $timeline);
    }
}
