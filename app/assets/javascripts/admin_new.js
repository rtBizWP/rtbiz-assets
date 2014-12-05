/**
 * Created by sai on 6/9/14.
 */
jQuery(function () {
    var file_frame_ticket;
    var rtassetAdmin = {

        init: function () {
            rtassetAdmin.initToolTop();
            rtassetAdmin.initDatePicket();
            rtassetAdmin.initDateTimePicker();
            rtassetAdmin.initMomentJS();
            rtassetAdmin.initattchmentJS();
            rtassetAdmin.initAssigneeSearch();
            rtassetAdmin.initVendorSearch();
            rtassetAdmin.initradiotax();
        },

        initToolTop: function () {
            jQuery(".tips, .help_tip").tipTip({
                'attribute': 'data-tip',
                'fadeIn': 50,
                'fadeOut': 50,
                'delay': 200
            });
        },
        initDatePicket: function () {
            if (jQuery(".datepicker").length > 0) {
                jQuery(".datepicker").datepicker({
                    'dateFormat': 'M d,yy',
                    onClose: function (newDate, inst) {

                        if (jQuery(this).hasClass("moment-from-now")) {
                            var oldDate = jQuery(this).attr("title");

                            if (newDate != "" && moment(newDate).isValid()) {
                                jQuery(this).val(moment(new Date(newDate)).fromNow());
                                jQuery(this).attr("title", newDate);

                                if (jQuery(this).next().length > 0) {
                                    jQuery(this).next().val(newDate);
                                }
                            } else if (oldDate != "") {
                                jQuery(this).val(moment(new Date(oldDate)).fromNow());
                                jQuery(this).attr("title", oldDate);

                                if (jQuery(this).next().length > 0) {
                                    jQuery(this).next().val(newDate);
                                }
                            }
                        }
                    }
                });
            }
            jQuery(".datepicker-toggle").click(function (e) {
                jQuery("#" + jQuery(this).data("datepicker")).datepicker("show");
            })
        },
        initDateTimePicker: function () {
            if (jQuery(".datetimepicker").length > 0) {
                jQuery(".datetimepicker").datetimepicker({
                    dateFormat: "M d, yy",
                    timeFormat: "hh:mm TT",
                    onClose: function (newDate, inst) {

                        var oldDate = jQuery(this).attr("title");

                        if (newDate != "" && moment(newDate).isValid()) {
                            jQuery(this).val(moment(new Date(newDate)).fromNow());
                            jQuery(this).attr("title", newDate);

                            if (jQuery(this).next().length > 0) {
                                if (jQuery(this).hasClass("moment-from-now")) {
                                    jQuery(this).next().val(newDate);
                                }
                            } else if (oldDate != "") {
                                jQuery(this).val(moment(new Date(oldDate)).fromNow());
                                jQuery(this).attr("title", oldDate);

                                if (jQuery(this).next().length > 0) {
                                    jQuery(this).next().val(newDate);
                                }
                            }
                        }
                    }
                });
            }
        },
        initMomentJS: function () {
            jQuery(document).on("click", ".moment-from-now", function (e) {
                var oldDate = jQuery(this).attr("title");

                if (oldDate != "") {
                    jQuery(this).datepicker("setDate", new Date(jQuery(this).attr("title")));
                }
            });

            jQuery(".moment-from-now").each(function () {

                if (jQuery(this).is("input[type='text']") && jQuery(this).val() != "") {
                    jQuery(this).val(moment(new Date(jQuery(this).attr("title"))).fromNow());
                } else if (jQuery(this).is(".comment-date")) {
                    jQuery(this).html(moment(new Date(jQuery(this).attr("title"))).fromNow());
                } else {
                    jQuery(this).html(moment(new Date(jQuery(this).html())).fromNow());
                }
            });
        },
        initattchmentJS: function () {
            jQuery(document).on('click', '.rthd_delete_attachment', function (e) {
                e.preventDefault();
                jQuery(this).parent().remove();
            });

            jQuery('#add_ticket_attachment').on('click', function (e) {
                e.preventDefault();
                if (file_frame_ticket) {
                    file_frame_ticket.open();
                    return;
                }
                file_frame_ticket = wp.media.frames.file_frame = wp.media({
                    title: jQuery(this).data('uploader_title'),
                    searchable: true,
                    button: {
                        text: 'Attach Selected Files',
                    },
                    multiple: true // Set to true to allow multiple files to be selected
                });
                file_frame_ticket.on('select', function () {
                    var selection = file_frame_ticket.state().get('selection');
                    var strAttachment = '';
                    selection.map(function (attachment) {
                        attachment = attachment.toJSON();
                        strAttachment = '<li data-attachment-id="' + attachment.id + '" class="attachment-item row_group">';
                        strAttachment += '<a href="#" class="delete_row rthd_delete_attachment">x</a>';
                        strAttachment += '<a target="_blank" href="' + attachment.url + '"><img height="20px" width="20px" src="' + attachment.icon + '" > ' + attachment.filename + '</a>';
                        strAttachment += '<input type="hidden" name="attachment[]" value="' + attachment.id + '" /></div>';

                        jQuery("#attachment-container .scroll-height").append(strAttachment);

                        // Do something with attachment.id and/or attachment.url here
                    });
                    // Do something with attachment.id and/or attachment.url here
                });
                file_frame_ticket.open();
            });
        },
        initAssigneeSearch: function () {
            try {
                if (arr_assignee_user != undefined) {
                    jQuery("#rt-asset-assignee").autocomplete({
                        source: function (request, response) {
                            var term = jQuery.ui.autocomplete.escapeRegex(request.term), startsWithMatcher = new RegExp("^" + term, "i"), startsWith = jQuery.grep(arr_assignee_user, function (value) {
                                return startsWithMatcher.test(value.label || value.value || value);
                            }), containsMatcher = new RegExp(term, "i"), contains = jQuery.grep(arr_assignee_user, function (value) {
                                return jQuery.inArray(value, startsWith) < 0 && containsMatcher.test(value.label || value.value || value);
                            });

                            response(startsWith.concat(contains));
                        },
                        focus: function (event, ui) {

                        },
                        select: function (event, ui) {
                            jQuery("#selected_assignee").html( "<div id='rt-asset-assignee-" + ui.item.id + "' class='assignee-list'>" + ui.item.imghtml + "<a class='assignee-title heading' target='_blank' href=''>"+ ui.item.label +"</a><input type='hidden' name='post_author' value='" + ui.item.id + "'/><a href='#removeAssignee' class='delete_row'>X</a></div>" );
                            jQuery("#rt-asset-assignee").val("");
                            return false;
                        }
                    }).data("ui-autocomplete")._renderItem = function (ul, item) {
                        return jQuery("<li></li>").data("ui-autocomplete-item", item).append("<a class='ac-subscribe-selected'>" + item.imghtml + "&nbsp;" + item.label + "</a>").appendTo(ul);
                    };

                    jQuery(document).on('click', "a[href=#removeAssignee]", function (e) {
                        e.preventDefault();
                        jQuery(this).parent().remove();
                    });

                }
            } catch (e) {

            }
        },
	    initVendorSearch: function () {
		    try {
			    if (arr_vendor_user != undefined) {
				    jQuery("#rt-asset-vendor").autocomplete({
	                      source: function (request, response) {
	                          var term = jQuery.ui.autocomplete.escapeRegex(request.term), startsWithMatcher = new RegExp("^" + term, "i"), startsWith = jQuery.grep(arr_vendor_user, function (value) {
	                              return startsWithMatcher.test(value.label || value.value || value);
	                          }), containsMatcher = new RegExp(term, "i"), contains = jQuery.grep(arr_vendor_user, function (value) {
	                              return jQuery.inArray(value, startsWith) < 0 && containsMatcher.test(value.label || value.value || value);
	                          });

	                          response(startsWith.concat(contains));
	                      },
	                      focus: function (event, ui) {

	                      },
	                      select: function (event, ui) {
		                      jQuery("#selected_vendor").html( "<div id='rt-asset-vendor-" + ui.item.id + "' class='vendor-list'>" + ui.item.imghtml + "<a class='vendor-title heading' target='_blank' href=''>"+ ui.item.label +"</a><input type='hidden' name='post[rtasset_vendor]' value='" + ui.item.id + "'/><a href='#removeVendor' class='delete_row'>X</a></div>" );
		                      jQuery("#rt-asset-vendor").val("");
	                          return false;
	                      }
	                 }).data("ui-autocomplete")._renderItem = function (ul, item) {
					    return jQuery("<li></li>").data("ui-autocomplete-item", item).append("<a class='ac-subscribe-selected'>" + item.imghtml + "&nbsp;" + item.label + "</a>").appendTo(ul);
				    };

				    jQuery(document).on('click', "a[href=#removeVendor]", function (e) {
					    e.preventDefault();
					    jQuery(this).parent().remove();
				    });

			    }
		    } catch (e) {

		    }
	    },
	    initradiotax : function() {
		    jQuery("input[name=tax_input\\[rt_device-type\\]\\[\\]]").click(function () {
			    selected = jQuery("input[name=tax_input\\[rt_device-type\\]\\[\\]]").filter(":checked").length;
			    if (selected > 1){
				    jQuery("input[name=tax_input\\[rt_device-type\\]\\[\\]]").each(function () {
					    jQuery(this).attr("checked", false);
				    });
				    jQuery(this).attr("checked", true);
			    }
		    });
		}
    }
    rtassetAdmin.init();
});