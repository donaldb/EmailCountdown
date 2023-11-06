# EmailCountdown

Creates an animated gif image that counts down to a specific date and time for use in email campaigns.
This only simulates a countdown clock in that it displays 60 frames and then it starts again.

## URL Parameters (*required)

* **time*** - Date & time when your countdown will end [e.g. 2023-12-31+23:59:59]
* **color** - Hex colour code for the text [default = 000000 (black)]
* **bg** - Hex colour code for the background [default = ffffff (white)]
* **fontname** - File name for the font to be used. The ttf file must be loaded into the /fonts folder [default = arial.ttf]
* **fontsize** - Point size of the font [default = 30]
* **frames** - Number of frames in the animation. Must be in the range between 60-120 [default = 60]

## Example

```<img src="http://server.com/countdown/gif.php?time=2023-12-31+23:59:59&color=ff0000&bg=000000&fontname=OpenSans-Regular-webfont.ttf&fontsize=30&frames=120" alt="Countdown">```

[Demo](https://donbraithwaite.com/EmailCountdown/index.html)
