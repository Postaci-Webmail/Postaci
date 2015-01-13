var databases = [
	{name:'bchm', collections:13, favorite:true},
	{name:'bchm-old', collections:11},
	{name:'e-store', collections:56},
	{name:'feathers', collections:5},
	{name:'kardon-db', collections:7},
	{name:'mma-db', collections:21, favorite:true},
	{name:'tv-guide', collections:31}
];

can.fixture({
  "GET /api/databases": function(){
    return databases;
  }
})