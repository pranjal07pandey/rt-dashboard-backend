$(function() {
    var helptrue = false;
    // define tour
    var tour = new Tour({

        debug: true,
        // template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Prev</button><span data-role='separator'></span><button class='btn btn-default' data-role='next'>Next »</button></div><button class='btn btn-default' data-role='end'>End tour</button></div>",
        // basePath: location.pathname.slice(0, location.pathname.lastIndexOf('/')),
        steps: [{
            element: "#first",
            title: "<span>1/9</span>",
            placement: "bottom",
            backdrop: true,
            modalId: "#myModal",
            // addbutton:".popupsecond",
            content: "Click “Add New” to add a new docket template",
            backdropPadding: 5,
            template: '<div class="popover fade top in" role="tooltip"> <div class="arrow"></div> <h3  class="popover-title"></h3> <div style="color: #000000" class="popover-content"></div> <div class="popover-navigation"> <div class="bootstro-nav-wrapper"><button class="btn btn-info btn-xs bootstro-finish-btn pull-left" data-role="end">Skip</button><button class="btn btn-info btn-xs bootstro-prev-btn" data-toggle="modal" data-target="#myModal" id="submitBtn"  data-role="next">Next →</button> <button style="margin-left: 41px;margin-bottom: 9px;" class="btn btn-info btn-xs bootstro-next-btn" data-role="prev">← Back</button>  <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> </div>  </div> </div>',

        },  {
            element: "#second",
            title: "<span>2/9</span>",
            placement: "bottom",
            backdrop: true,
            modalId: "#myModal",
            content: "Add a relevant and easy to remember “Docket Title”",
            backdropPadding: 5,
            template: '<div class="popover fade top in" role="tooltip"> <div class="arrow"></div> <h3  class="popover-title"></h3> <div style="color: #000000" class="popover-content"></div> <div class="popover-navigation"> <div class="bootstro-nav-wrapper"><button class="btn btn-info btn-xs bootstro-finish-btn pull-left" data-role="end">Skip</button><button class="btn btn-info btn-xs bootstro-prev-btn"   data-role="next">Next →</button> <button style="margin-left: 41px;margin-bottom: 9px;" class="btn btn-info btn-xs bootstro-next-btn closeModal" data-role="prev">← Back</button>  <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> </div>  </div> </div>',


        }],
        onNext: function (tour) {
            if($(this)[0]["modalId"]){
                $("#helpFlag").val('true');
            }
        },
        onPrev: function (tour) {
            $($(this)[0]["modalId"]).modal('hide');
        },
        onEnd: function (tour) {
            $("#myModal").modal('hide');
        }

    });

    // init tour
    // tour.init();

    // start tour
    $('#start-tour').click(function() {
        tour.restart();
        helptrue    = true;
        $("#myModal").modal({
            show: false,
            backdrop: 'static'
        });

    });

    $('.popupsecond').click(function () {

        $('#myModal').modal('show')
        if(helptrue==true) {
            tour.goTo(1);
        }
    });


});