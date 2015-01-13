'use strict';

export var Server = can.Model.extend('Server', {
	resource: '/api/amity/servers'
}, {});

export var Database = can.Model.extend('Database', {
	resource: '/api/databases'
}, {});

export var Collection = can.Model.extend('Collection', {
	resource: '/api/collections'
}, {});

export var Doc = can.Model.extend('Doc', {
	resource: '/api/documents'
}, {});


export var Task = can.Feathers.Model.extend('tasks', {
	resource: '/api/tasks'
}, {});

export var Todo = can.Feathers.Model.extend('todos', {
	resource: '/api/todos'
}, {});
