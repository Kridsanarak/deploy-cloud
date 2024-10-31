<!-- Include FullCalendar CSS -->
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
<!-- Include jQuery -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
<!-- Include FullCalendar -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.min.css' rel='stylesheet'
    media='print' />

<script>
    $(document).ready(function () {
        $('#calendar').fullCalendar({
            // Calendar settings
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay'
            },
            defaultDate: '<?php echo date('Y-m-d'); ?>', // Set default date to today
            navLinks: true, // Enable navigation links
            editable: false, // Disable editability
            eventLimit: true, // Show "more" link when there are too many events

            // Fetch events from tasks.php
            events: 'tasks.php',
            eventRender: function (event, element) {
                // Customize the event display to show only floor_id
                element.find('.fc-title').text('Floor: ' + event.floor_id);
            }
        });
    });
</script>