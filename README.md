[![Build Status](https://travis-ci.org/nuxia/BusinessDayManipulator.svg?branch=master)](https://travis-ci.org/nuxia/BusinessDayManipulator)

# Manipulate easily working days

* Find number of working days for a period.
* Find the ending date by adding the man day quantity to the starting date

## Simple Manipulator
```php
use Nuxia\BusinessDayManipulator\Manipulator;

$holidays = [
    new DatePeriod(new \DateTime('2015-02-02'), new \DateTime('2015-02-06')),
    new \DateTime('2015-02-20')
];

$freeDays = [
    new DatePeriod(new \DateTime('2015-02-24'), new \DateTime('2015-02-27'))
];

$freeWeekDays = [
    Manipulator::SATURDAY,
    Manipulator::SUNDAY
];

$manipulator = new Manipulator($freeDays, $freeWeekDays, $holidays);
```

- **Free week days are repeated every week**
- **Holidays are repeated every year**
- **Free days are not repeated**

## Localized Manipulator

```php
use Nuxia\BusinessDayManipulator\LocalizedManipulator;

$holidays = [
    new DatePeriod(new \DateTime('2015-02-02'), new \DateTime('2015-02-06')),
    new \DateTime('2015-02-20')
];

$freeDays = [
    new DatePeriod(new \DateTime('2015-02-24'), new \DateTime('2015-02-27'))
];

$freeWeekDays = null; //If null, we will automatically set free week days from the locale.

//Guess automatically free week days.
$localizedManipulator = new LocalizedManipulator('Europe/Paris', 'fr', $freeDays, $freeWeekDay, $holidays);
```

## Working Day Date Predicator

```php
$manipulator->setStartDate(new \DateTime('2015-02-01'));
$manipulator->addBusinessDays(15);
$manipulator->getDate()->format('Y-m-d'); //2015-03-06
```

```php
$manipulator->setStartDate(new \DateTime('2015-03-06'));
$manipulator->subBusinessDays(15);
$manipulator->getDate()->format('Y-m-d'); //2015-02-01
```

## Working Day Quantity Predicator
```php
$manipulator->setStartDate(new \DateTime('2015-02-01'));
$manipulator->setEndDate(new \DateTime('2015-03-02'));

$manipulator->getBusinessDays(); //12
$manipulator->getBusinessDaysDate()); //Array of DateTime instance
```

## Manipulator::isBusinessDay()
```php
$manipulator->isBusinessDay(new \DateTime('2015-02-08')); //false
```

## Manipualtor::getTypeOfDay()
```php
$manipulator->getTypeOfDay(new \DateTime('2015-02-08')); // free_week_day
```
## Running test

Simply run `phpunit` to execute unit test

## License
```txt
The MIT License (MIT)

Copyright (c) 2015 Nuxia

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```
