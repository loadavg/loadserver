# LoadAvg Server

The next generation of server monitoring and analytics.

LoadAvg is free software that will change the way you manage your servers. Your
server is a complex machine and you need to understand what keeps it running and
what resources are being used in order to give it all she's got.

LoadAvg helps you to manage performance and stay on top of your hardware. It helps
you to view resource trends and patterns so you can easily pinpoint high resource
usage times, and its powerful charts and graphs allow you to visually access daily
load and memory usage.

  * Load Management
  * Server Analytics
  * Server Security

## Setup and Installation

First off, you'll need to have an apache web server running PHP. You'll also need
to import the database schema `loadavg_server.sql` located in the `schema/` directory
into your MySQL database. Next, you'll want to rename the `settings.example.ini` file
under the `config/` directory to `settings.ini` and modify the values inside to match
your database environment setup accordinly.