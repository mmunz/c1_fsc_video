mod {
    web_layout.tt_content.preview.video = EXT:c1_fsc_video/Resources/Private/Templates/Preview/FscVideo.html
    wizards.newContentElement.wizardItems.common {
        elements {
            c1_fsc_video {
                iconIdentifier = c1_fsc_video
                title       = LLL:EXT:c1_fsc_video/Resources/Private/Language/TCA.xlf:c1_fsc_video
                description = LLL:EXT:c1_fsc_video/Resources/Private/Language/TCA.xlf:c1_fsc_video_description
                tt_content_defValues {
                    CType = c1_fsc_video
                }
            }
        }
        show := addToList(c1_fsc_video)
    }
}


