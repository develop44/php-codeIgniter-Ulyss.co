<?php header('Access-Control-Allow-Origin: *'); ?>
<div class="cal-week-box">
	<div class="cal-offset1 cal-column"></div>
	<div class="cal-offset2 cal-column"></div>
	<div class="cal-offset3 cal-column"></div>
	<div class="cal-offset4 cal-column"></div>
	<div class="cal-offset5 cal-column"></div>
	<div class="cal-offset6 cal-column"></div>
	<div class="cal-row-fluid cal-row-head">
		<% _.each(days_name, function(name) { %>
			<div class="cal-cell1 <%= cal._getDayClass('week', start) %>" data-toggle="tooltip" title="<%= cal._getHolidayName(start) %>"><%= name %><br>
				<small><span data-cal-date="<%= start.getFullYear() %>-<%= start.getMonthFormatted() %>-<%= start.getDateFormatted() %>" data-cal-view="day"><%= start.getDate() %> <%= cal.locale['ms' + start.getMonth()] %></span></small>
			</div>
			<% start.setDate(start.getDate() + 1); %>
		<% }) %>
	</div>
	<hr>
	<%= cal._week() %>
</div>
