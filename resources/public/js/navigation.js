"use strict";
/* global jQuery:true */

if (typeof jQuery !== "undefined")
{
    jQuery(
        function ($)
        {
            var $navigation = $("#gluggi-navigation");
            var $navigationElements = $navigation.find(".gluggi-navigation-elements");
            var $button = $("#gluggi-navigation-button");
            var $body = $(document.body);
            var buttonTexts = {
                open: $button.text(),
                close: "Close nav"
            };

            function initializeNavigation ()
            {
                $(".gluggi-element").each(
                    function (index, element)
                    {
                        var $element = $(element);

                        $navigationElements.append($("<a></a>", {
                            href: "#" + element.id,
                            text: $element.find(".gluggi-element-title").text()
                        }));
                    }
                );
            }

            function toggleNavigation ()
            {
                var fun = $navigation.hasClass("is-visible") ? closeNavigation : openNavigation;
                fun();
            }

            function openNavigation ()
            {
                $navigation.addClass("is-visible");
                $button.text(buttonTexts.close);

                window.setTimeout(
                    function ()
                    {
                        $body.on("click", closeNavigation);
                    },
                    0
                );
            }

            function closeNavigation ()
            {
                $navigation.removeClass("is-visible");
                $body.off("click", closeNavigation);
                $button.text(buttonTexts.open);
            }

            function preventClick (event)
            {
                event.stopPropagation();
            }


            initializeNavigation();
            $button.on("click", toggleNavigation);
            $navigation
                .on("click", "a", closeNavigation)
                .on("click", preventClick);
        }
    );
}
