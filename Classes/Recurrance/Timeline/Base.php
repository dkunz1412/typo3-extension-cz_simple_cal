<?php
namespace Tx\CzSimpleCal\Recurrance\Timeline;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Tx\CzSimpleCal\Domain\Model\Enumeration\EventStatus;

/**
 * a class representing a timeline of events
 *
 * so that's basically a collection of events with a start and an end, that are sorted by their start dates
 */
class Base implements \Iterator, \Countable {

	/**
	 * holds all timespans
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * it is recommended to add the entries ordered.
	 * if this is not done, this property remembers it and will force a sorting before
	 * the entry is accessed for output
	 *
	 * @var boolean
	 */
	protected $sortNeeded = FALSE;

	/**
	 * used in conjunction with $sortNeeded.
	 * This value stores the start of the last known event. This way it can check
	 * if all entries were submittet in ascending order
	 *
	 * @var integer
	 */
	protected $lastValue = 0;

	/**
	 * don't output the next but the current value of data if the next is requested
	 *
	 * @ugly
	 * @see \Tx\CzSimpleCal\Recurrance\Timeline\Base::next()
	 * @var boolean
	 */
	protected $nextAsCurrent = FALSE;

	/**
	 * add an EventIndex to the collection
	 *
	 * @param array $data
	 * @param \Tx\CzSimpleCal\Domain\Interfaces\IsRecurring|\Tx\CzSimpleCal\Domain\Model\Event $event
	 * @return Base
	 */
	public function add($data, $event) {
		$data = $this->cleanData($data);
		$this->isDataValid($data);
		$this->data[$data['start']] = $data;
		if ($data['start'] < $this->lastValue) {
			$this->sortNeeded = TRUE;
		}
		$this->lastValue = $data['start'];
		$this->initAdditionalEventData($event);
		return $this;
	}

	/**
	 * clean the input data
	 *
	 * @param array $data
	 * @return array
	 */
	protected function cleanData($data) {
		$data['start'] = intval($data['start']);
		$data['end'] = intval($data['end']);
		return $data;
	}

	/**
	 * check if the given data is valid
	 *
	 * @param $data
	 * @return bool
	 * @throws \UnexpectedValueException
	 */
	protected function isDataValid($data) {
		if (!array_key_exists('start', $data)) {
			throw new \UnexpectedValueException('"start" is required.');
		}
		if (!array_key_exists('end', $data)) {
			throw new \UnexpectedValueException('"end" is required.');
		}

		if ($data['start'] == 0) {
			throw new \UnexpectedValueException('"start" should not be "0".');
		}
		if ($data['end'] == 0) {
			throw new \UnexpectedValueException('"end" should not be "0".');
		}

		if ($data['start'] > $data['end']) {
			throw new \UnexpectedValueException(sprintf('"start" should not be later than "end". (%d, %d)', $data['start'], $data['end']), 1392817280);
		}

		if (array_key_exists($data['start'], $this->data)) {
			throw new \UnexpectedValueException(sprintf('A timespan with start %d already exists.', $data['start']));
		}

		return TRUE;
	}

	/**
	 * This method currently does nothing and should be overwritten by subclasses if required.
	 *
	 * It can be used to load additional event data in the data array.
	 *
	 * @param \Tx\CzSimpleCal\Domain\Model\BaseEvent $event
	 */
	protected function initAdditionalEventData($event) {
	}

	/**
	 * initializes output by sorting the array
	 */
	protected function initOutput() {
		if (!$this->sortNeeded) {
			return;
		}
		ksort($this->data);
		$this->sortNeeded = FALSE;
	}

	/**
	 * returns the data as an array
	 * (for debugging only - this class already behaves as if it was an array)
	 *
	 * @internal
	 * @return array
	 */
	public function toArray() {
		return $this->data;
	}

	/**
	 * check if this has data
	 *
	 * @return boolean
	 */
	public function hasData() {
		return count($this->data) > 0;
	}

	/**
	 * unset the entry that the array-pointer points at
	 */
	public function unsetCurrent() {
		unset($this->data[key($this->data)]);
		// see description for next() on why this property is set
		$this->nextAsCurrent = TRUE;
	}

	/**
	 * Merges the given additional data in the current entry.
	 *
	 * @param array $additionalData
	 */
	public function mergeAdditionalDataToCurrent(array $additionalData) {
		$this->data[key($this->data)] = array_merge($this->data[key($this->data)], $additionalData);
	}

	/* implement Countable */

	public function count() {
		return count($this->data);
	}

	/* implement Iterator */

	public function rewind() {
		$this->initOutput();
		reset($this->data);
	}

	public function current() {
		$this->initOutput();
		return current($this->data);
	}

	public function key() {
		$this->initOutput();
		return key($this->data);
	}

	public function next() {
		$this->initOutput();

		/* @ugly:
		 *
		 * when unsetting an entry while iterating over the array all other entries will shift on
		 * position back. This also affects the internal pointer that points to the next entry
		 * if the if the "current()" or any entry before is removed.
		 * So when an entry is deleted and next() is called - one entry will be skipped.
		 *
		 * To avoid that the property "nextAsCurrent" is used. It will call "current()" instead of
		 * "next()" if an entry was deleted.
		 *
		 * In PHP5.3 this procedure is not needed if you use SplDoublyLinkedList
		 */
		if ($this->nextAsCurrent) {
			$this->nextAsCurrent = FALSE;
			return current($this->data);
		}
		return next($this->data);
	}

	public function valid() {
		$this->initOutput();
		return NULL !== key($this->data);
	}
}