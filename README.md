Events-Calendar
===============

A versatile and accessible events calendar for Lepton an WebsiteBaker

Requirements
============

0. This module, of course
1. WebsiteBaker or Lepton CMS
2. Dwoo Template engine: https://github.com/phpManufaktur/Dwoo/
3. Droplet(s) to place calendar somewhere on your website
4. PHP 5.2 or above

2012-09-03
Moved project to Github: https://github.com/fberke/Events-Calendar

2012-05-19 - 2012-08-29 fberke
- frontend output now is template based to give you full access to the screen appearance - DWOO required!
- re-arranged backend, esp. custom fields
- date and time is now stored in Unix time format in the database, i.e. in a single value
  date format in backend is Y-n-j internally and Y-m-d on screen
  the frontend date format can still be chosen in backend
- Moved settings from 'modify_customs' to 'modify_settings'
- Created an input/select combo for image size as seen on http://www.cs.tut.fi/~jkorpela/forms/combo.html
- Quick delete for events in backend
- removed lots of unused code and cleaned up/optimized what was still needed
- changed several default values
- many database changes (names and data types)
- new method for chaning event time

2012-05-18 fberke
- Cleaned up language files and re-arranged language array to meet eventual appearance in files

2012-05-15 fberke
- Added setting to hide previous calendar events