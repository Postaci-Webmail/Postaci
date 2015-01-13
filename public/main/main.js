'use strict';

import 'mustache-helpers';
import 'routes';
import './styles/material-font-icons-only.css!';

/* * * Fixtures * * */
import 'fixtures/databases';
import 'fixtures/collections';
import 'fixtures/documents';

/* * * Components * * */
import 'components/page-home/page-home';
import 'components/sidebar-main/sidebar-main';
import 'components/message-list/message-list';
import 'components/message-view/message-view';
import 'components/account-menu/account-menu';
import 'components/message-search/message-search';

import 'components/task-module/task-module';
import 'components/todo-module/todo-module';

/* * * Main Application State * * */
import appState from 'appState';

$(document.body).append( can.view('main/site.stache', appState) );


appState.bind('page', function(ev, newVal){
  if(newVal) {
    var template =  '<page-'+newVal+'></page-'+newVal+'>';
    $('#content').html(  can.stache( template )( appState ) );
  }
});

can.route.ready();
