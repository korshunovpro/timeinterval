# timeinterval

 Class to work with amount of time(hours, minutes, seconds). Absolute time interval not related to calendar
 (year, month). 
 Features: 
 - Create from: seconds, datestring, interval spec string
 - convert to hours, minutes, seconds
 - format compatible with DateInterval and additional format placeholder: %x, %X amount of minutes
 - positive and negative intervals

 Instance:
 1) TimeInterval instance, can create by seconds, example:
 new TimeInterval([int seconds])

 or other time units, example:
 new TimeInterval(1, TimeInterval::HOUR)
 
 2) TimeInterval instance, can create by H:M:S string, example:
 TimeInterval::createFromHMS('-122:45') - negative interval
 TimeInterval::createFromHMS('122:45')
 TimeInterval::createFromHMS('122:45:58')

 3) TimeInterval instance, can create by datestring, example:
 TimeInterval::createFromDateString('1 day + 12 hours');

 4) TimeInterval instance, can create by interval spec string, example:
 TimeInterval::createFromIntervalSpec('P1DT12H');

 Can work with positive and negative intervals.
 Add and sub intervals:
 1) Add: TimeInterval::add(TimeInterval $time)
 2) Sub: TimeInterval::sub(TimeInterval $time)
 3) Modify: TimeInterval::modify(-1, TimeInterval::HOUR).

 Formatting TimeInterval::format()
 Format compatible with \DateInterval::format(), but only time units placeholder.
 Available placeholders: %r, %R, %h, %H, %m, %M, %s, %S
 Additional placeholders: %x, %X - amount of minutes in the interval(round to interger)
