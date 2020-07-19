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
