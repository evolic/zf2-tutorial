## ZF2 tutorial application using jQuery FullCalendar ##


### About the project ###

It is ZF2 tutorial project, which was extended with album's songs.

There is also *EvlCalendar* module, which contains

*   Polish holidays list/calendar (read only)
*   and also events calendar, where you can add/edit single events.



### Technology ###

Project uses Doctrine 2 ORM Entities instead of Zend\Db class.

In project I used modified jQuery FullCalendar, version 1.6.2 to enable support for Zend **ViewJsonStrategy**.



### Building project ###

1.  Downloading dependencies

    Please use **update-libs** program (Bash/Windows Batch) instead of **php composer.phar update**

    ```shell
    ./update-libs.sh
    ```

    or 

    ```shell
    ./update-libs.bat
    ```

    or double click on **update-libs** program.

    Included script creates **log file**, which should be very helpful when you encounter some issues
    with the most recent version of some libraries.
    Thanks to **the log file** you will be able to return to **the last good working environment**.

2.  Creating database structure

    This application has two modules, which use database:

*   Album
    SQL file can be found at **module/Album/data/album.sql**

*   EvlCalendar
    SQL file can be found at **module/EvlCalendar/data/calendar.sql**

    SQL files contain also some sample records.



### TODO list ###

*   ~~Integrate with Twitter Bootstrap framework~~
*   ~~Add edit/delete event actions~~
*   Add working with events, which belongs to different calendars/domain


### More info ###

See [http://akrabat.com/zend-framework-2-tutorial/](http://akrabat.com/zend-framework-2-tutorial/)