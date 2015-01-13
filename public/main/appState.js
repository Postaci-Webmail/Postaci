'use strict';

import '../../models/models';

// Resolve this with the collections when the database changes.
var colDef = new can.Deferred();

// Defines the state of the application
var AppState = can.Map.extend({
	define : {
		servers:{
			value: new Server.List(),
			serialize:false
		},

		server:{
			set(value){
				this.attr('hostname', value.hostname);
				return value;
			},
			serialize:false
		}
	},

});

var appState = new AppState();
export default appState;

can.route.map(appState);
