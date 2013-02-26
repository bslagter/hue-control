# hue-control

## Goal

We want to be able to control all lights in our house using a user friendly interface on our mobile devices.

## Architecture

API-first design
* RESTful interface
** Web interface (JS Application)
** Mobile apps
* Command line interface
** Testing and cronjobs
** Pass external events (like RF or IR signals)

Model
* Bridges (for controlling lights)
** Philips Hue
** Klik-aan-klik-uit
* Lights
* Groups
* Scenes
* Schedules