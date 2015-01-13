'use strict';

import {Task} from '../../models/models';

var tasks = new Task.List({});

can.Component.extend({
	tag: 'task-module',
	template: can.view('/main/components/task-module/task-module.stache'),
	scope: {
		task:new Task({}),
		tasks:tasks,
		saveTask: function(scope, el, ev){
			ev.preventDefault();
			scope.save();
			this.attr('task', new Task({}));
		},
		updateTask:function(scope, el, ev){
			scope.save();
		},
		deleteTask: function(scope, el, ev){
			scope.destroy();
		},
		noNewline: function(scope, el, ev){
			if (ev.which == 13) {
				el.blur();
				return false;
			} else {
				return true;
			}
		}
	}
});

Task.bind('created', function(ev, newTask) {
	tasks.push(newTask);
});