var collections = [
	{name:'account_shared_names', documents:2},
	{name:'accounts', documents:19},
	{name:'assets', documents:1242},
	{name:'budget', documents:855},
	{name:'calendar', documents:57},
	{name:'categories', documents:411},
	{name:'contacts', documents:1001},
	{name:'credit_scores', documents:141},
	{name:'debt', documents:144},
	{name:'labels', documents:1},
	{name:'planning', documents:55},
	{name:'preferences', documents:991},
	{name:'reports', documents:1444},
	{name:'sessions', documents:144242},
	{name:'system.indexes', documents:555},
	{name:'system.users', documents:155},
	{name:'trash', documents:0}
];

can.fixture({
  "GET /api/collections": function(){
    return collections;
  }
})