# Cake Day Scheduler
## Getting Started
### Pre requirements
- PHP 7.4
- Composer

### Instillation
From the projects root directory run
```bash
composer install
```

### Usage
Run Command
```bash
php cake-day.php "my-input-file.txt" "my-output-file.csv"
```

Get Help
```bash
php cake-day.php -h
```

## Design Decisions
## Omissions + Improvements
I was a little pushed for time, but if I had more I would like to address the following.

- The tests are minimal and mostly just a feature test of CakeDayScheduleBuilder but this has given me a fairly high 
level of confidence in the application, but I wouldn't be surprised if theirs are a couple of bugs in their.

- Handle the previous years unscheduled birthdays - Currently birthdays from the previous year that needed rescheduling 
into the current year do not appear in the schedule.

- Leap years/days - Not sure how the application would handle a leap year or people born on a leap day, minor changes 
maybe needed.
 
- CakeDayScheduleBuilder - You could probable argue that this class is doing a little too much work in terms of single 
responsibility principle. I'd be slightly tempted to move a lot of the logic into separate classes which would help with 
testing.

### Architecture
We talked a little about Domain Driven Development, so I though this maybe a good chance to show a couple of concepts.

As discussed it can be a bit time-consuming, so it's not how I'd approach every problem that comes my way, but it does 
seem to simplify things which I think has help keep the application relatively clean.

#### Dependencies:
I prefer not to bring in dependencies unless they provide a very tangible benefit that out weight the long term 
maintenance headache they can sometimes cause.

Below is my reasoning for the choosing each dependency.

- symfony/console - Helps to create a "high quality" console apps ver quickly compared to doing it from scratch. (It's 
what Laravel uses under the hood).

- nesbot/carbon - The app's logic is mostly around dates, so I've chosen to use Carbon over vanilla DateTime as it 
provides a lot very helpful functionality compared to DateTime.

- doctrine/collections - I just wanted a collection to extend without writing my own abstract collection. TBH 
extending doctrines one hasn't worked so well as the map and filter functions are a bit broken due to the way 
I've implemented it.

- league/csv - It's not too hard to write a csv from scratch in php however league csv provides a few benefits mostly 
around escaping + avoiding [csv injection](https://owasp.org/www-community/attacks/CSV_Injection) that are a little 
time-consuming to do from scratch.  
