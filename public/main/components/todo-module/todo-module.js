'use strict';

import {Todo} from '../../models/models';

var todos = new Todo.List({});

can.Component.extend({
	tag: 'todo-module',
	template: can.view('/main/components/todo-module/todo-module.stache'),
	scope: {
		todo:new Todo({}),
		todos:todos,
		saveTodo: function(scope, el, ev){
			ev.preventDefault();
			scope.save();
			this.attr('todo', new Todo({}));
		},
		updateTodo:function(scope, el, ev){
			scope.save();
		},
		deleteTodo: function(scope, el, ev){
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

Todo.bind('created', function(ev, newTodo) {
	todos.push(newTodo);
});