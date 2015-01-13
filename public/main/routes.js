/* * * Auth * * */
can.route('passwordemail/:email',{page: 'passwordemail'});
can.route('passwordchange/:secret',{page: 'passwordchange'});
can.route('verify/:secret',{page: 'verify'});
// can.route(':page',{page: 'overview'});


/* * * Overview * * */
can.route('', {'page':'home'});
can.route('settings', {'page':'settings'});
can.route('help', {'page':'help'});

can.route(':hostname', {'page':'server'});
can.route(':hostname:db_name', {'page':'database'});
can.route(':hostname:db_name/:col_name', {'page':'collection'});
can.route(':hostname:db_name/:col_name/:doc_id', {'page':'document'});

