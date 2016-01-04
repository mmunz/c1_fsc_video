# Responsive Youtube/Vimeo Video Embed with cover image

This extension adds a video element to include vimeo and youtube videos in a
responsive way.

## Features
* uses TYPO3's new (7.5) media elements for videos as much as possible
* When adding a video in the backend a preview is automatically downloaded and
  a FAL-reference for the preview image is created
* If autoplay is not set, then the cover image and a play button is shown. The
  video is only loaded when the user clicks on the play button.
* uses f:media to render the preview images, hence it can make use of
  https://github.com/mmunz/c1_fluid-styled-responsive-images to render the
  previews with the srcset attribute.

## Autoplay and mobile devices
Most mobile browsers do not allow autoplay to save bandwidth. Thats annoying and
i have not found a workaround yet. Mobile Users need to click play twice (once
for loading the player iframe, the other click to actually start the video).


