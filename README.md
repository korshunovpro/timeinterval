# timeinterval

Class to work with amount of time(hours, minutes, seconds). Absolute time interval not related to calendar
(year, month). 
Features: 
- Create from: seconds, datestring, interval spec string
- convert to hours, minutes, seconds
- format compatible with DateInterval and additional format placeholder: %x, %X amount of minutes
- positive and negative intervals

Instance:
------

1) TimeInterval instance, can create by seconds, example:
```php
$time = new TimeInterval(123);
```
or other time units, example:
```php

$time = new TimeInterval(1, TimeInterval::HOUR);
```
2) TimeInterval instance, can create by H:M:S string, example:
```php
$time = TimeInterval::createFromHMS('-122:45'); // negative interval
$time = TimeInterval::createFromHMS('122:45');
$time = TimeInterval::createFromHMS('122:45:58');
```

3) TimeInterval instance, can create by datestring, example:
```php
$time = TimeInterval::createFromDateString('1 day + 12 hours');
```

4) TimeInterval instance, can create by interval spec string, example:
```php
$time = TimeInterval::createFromIntervalSpec('P1DT12H');
```

Can work with positive and negative intervals.

Add and sub intervals
------
1) Add:
```php
$time1 = new TimeInterval(1, TimeIntervalInterface::HOUR);
$time2 = new TimeInterval(60, TimeIntervalInterface::MINUTE);
$time1->add($time2);

echo $time1->convertToSeconds(); // 7200
```
2) Sub:
```php
$time1 = new TimeInterval(1, TimeIntervalInterface::HOUR);
$time2 = new TimeInterval(60, TimeIntervalInterface::MINUTE);
$time1->sub($time2);

echo $time1->convertToSeconds(); // 0
```

3) Modify:
```php
$time = new TimeInterval(1, TimeIntervalInterface::HOUR);
$time->modify(-1, TimeIntervalInterface::HOUR);

echo $time->convertToSeconds(); // 0
```

Formatting
------
Format compatible with \DateInterval::format(), but only time units placeholder.
Available placeholders: %r, %R, %h, %H, %m, %M, %s, %S
Additional placeholders: %x, %X - amount of minutes in the interval(round to interger)

```php
$time = new TimeInterval(3600 + 60 + 55);
echo $time->format('%H:%I:%S'); // 01:01:55
```

Example
------
```php
use Korshunov\TimeInterval\TimeInterval;
use Korshunov\TimeInterval\TimeIntervalImmutable;
use Korshunov\TimeInterval\TimeIntervalInterface;

$days = 5;
$hours = 8;
$minutes = 15;
$seconds = 15;

// BASE
$convertToSeconds = $days * 86400 + $hours * 3600 + $minutes * 60 + $seconds;

$time = new TimeInterval($convertToSeconds);

echo $time->convertToHours(); // 128
// with precision
echo $time->convertToHours(2); //128.25
echo $time->convertToHours(3); //128.254

echo $time->convertToMinutes(); // 7695
// with precision
echo $time->convertToMinutes(2); // 7695.25
echo $time->convertToMinutes(3); // 7695.25

echo $time->convertToSeconds(); // 461715

// MODIFY
$time = new TimeInterval($days, TimeIntervalInterface::DAY);
$time->modify($hours, TimeIntervalInterface::HOUR);
$time->modify($minutes, TimeIntervalInterface::MINUTE);
$time->modify($seconds, TimeIntervalInterface::SECOND); // or $time->modify($seconds);

echo $time->convertToHours();   // 128
// with precision
echo $time->convertToHours(2); //128.25
echo $time->convertToHours(3); //128.254

echo $time->convertToMinutes(); // 7695
// with precision
echo $time->convertToMinutes(2); // 7695.25
echo $time->convertToMinutes(3); // 7695.25

echo $time->convertToSeconds(); // 461715

// ADD
$time = new TimeInterval();
$time1 = new TimeInterval($days, TimeIntervalInterface::DAY);
$time2 = new TimeInterval($hours, TimeIntervalInterface::HOUR);
$time3 = new TimeInterval($minutes, TimeIntervalInterface::MINUTE);
$time4 = new TimeInterval($seconds, TimeIntervalInterface::SECOND);

// adding
$time->add($time1)->add($time2);
$time->add($time3);
$time->add($time4);

echo $time->convertToHours(); // 128
// with precision
echo $time->convertToHours(2); //128.25
echo $time->convertToHours(3); //128.254

echo $time->convertToMinutes(); // 7695
// with precision
echo $time->convertToMinutes(2); // 7695.25
echo $time->convertToMinutes(3); // 7695.25

echo $time->convertToSeconds(); // 461715

// SUB
$time = new TimeInterval($convertToSeconds);
$time1 = new TimeInterval($days, TimeIntervalInterface::DAY);
$time2 = new TimeInterval($hours, TimeIntervalInterface::HOUR);
$time3 = new TimeInterval($minutes, TimeIntervalInterface::MINUTE);
$time4 = new TimeInterval($seconds, TimeIntervalInterface::SECOND);

// adding
$time->sub($time1)->sub($time2);
$time->sub($time3);
$time->sub($time4);

echo $time->convertToSeconds(); // 0

// Immutable
$timeImmutable = new TimeIntervalImmutable($convertToSeconds);
$time1 = new TimeInterval($days, TimeIntervalInterface::DAY);
$time2 = new TimeInterval($hours, TimeIntervalInterface::HOUR);
$time3 = new TimeInterval($minutes, TimeIntervalInterface::MINUTE);
$time4 = new TimeInterval($seconds, TimeIntervalInterface::SECOND);

$timeNew = $timeImmutable->sub($time1)->sub($time2)->sub($time3)->sub($time4);

echo $timeImmutable->convertToSeconds(); // 461715
echo $timeNew->convertToSeconds(); // 0

// Create from HMS
$time = TimeInterval::createFromHMS('24:05:15');
echo $time->convertToSeconds(); // 86715

$time = TimeInterval::createFromHMS('-24:05:15');
echo $time->convertToSeconds(); // -86715

// Create from date string
$time = TimeInterval::createFromDateString('24 hours + 5 minutes + 15 seconds');
echo $time->convertToSeconds(); // 86715

// Create from interval spec
$time = TimeInterval::createFromIntervalSpec('P1DT0H5M15S');
echo $time->convertToSeconds(); // 86715

// Format
$time = new TimeInterval($days, TimeIntervalInterface::DAY);
$time->modify($hours, TimeIntervalInterface::HOUR);
$time->modify($minutes, TimeIntervalInterface::MINUTE);
$time->modify($seconds, TimeIntervalInterface::SECOND);

echo $time->getHours(); // 128
echo $time->getMinutes(); // 15
echo $time->getSeconds(); // 15

echo $time->format('%H:%I:%S'); // 128:15:15
echo $time->format('%x min. %s sec.'); // 7695 min. 15 sec.

$timeNegative = new TimeInterval();
$timeNegative->sub($time);

echo $timeNegative->format('%r%H:%I:%S'); // -128:15:15
echo $timeNegative->format('%r%x min. %s sec.'); // -7695 min. 15 sec.