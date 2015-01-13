'use strict';

import appState from '../../appState';

can.Component.extend({
	tag: 'page-home',
	template: can.view('/main/components/page-home/page-home.stache'),
	scope: {
		appState: appState,

		selectDB: function(scope, el, ev){
			can.route.attr('db_name', scope.name);
			can.route.attr('page', 'database');
		}
	},
	events: {},
	helpers:{}
});
