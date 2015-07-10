"use strict";
/* global jQuery:true gluggi:true */

if (typeof jQuery !== "undefined")
{
    jQuery(
        function ($)
        {
            var $searchElements = $(".gluggi-navigation-elements a");
            var $search = $("#gluggi-navigation-filter");
            var timer = null;
            var searchIndex = prepareSearchIndex();
            var searchDelay = 250;


            $search.on("input", onSearchInput);

            function prepareSearchIndex ()
            {
                var searchIndex = lunr(
                    function ()
                    {
                        this.field("title");
                    }
                );

                $searchElements.each(
                    function (index, element)
                    {
                        searchIndex.add(
                            {
                                id: index,
                                title: $(element).text()
                            }
                        );
                    }
                );

                return searchIndex;
            }


            function onSearchInput (event)
            {
                clearTimeout(timer);

                timer = setTimeout(function() {
                    search(event);
                }, searchDelay);
            }

            /**
             * Handles the search and visiblility of the elements
             *
             * @param event
             */
            function search (event)
            {
                var searchValue = $(event.currentTarget).val();
                $searchElements.show();

                if ($.trim(searchValue))
                {
                    var results = searchIndex.search(searchValue);

                    $searchElements.hide();

                    $.each(results,
                        function (i, result)
                        {
                            $($searchElements[result.ref]).show();
                        }
                    );
                }
            }
        });
}
