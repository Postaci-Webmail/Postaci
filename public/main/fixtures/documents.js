var documents = [
	{_id:'ObjectID("5359550868cc9b2b372e2ea1")', text:''},
	{_id:'ObjectID("5359550868cc9b2b372e2ea2")', text:''},
	{_id:'ObjectID("5359550868cc9b2b372e2ea3")', text:''},
	{_id:'ObjectID("5359550868cc9b2b372e2ea4")', text:''},
	{_id:'ObjectID("5359550868cc9b2b372e2ea5")', text:''},
	{_id:'ObjectID("5359550868cc9b2b372e2ea6")', text:''},
	{_id:'ObjectID("5359550868cc9b2b372e2ea7")', text:''},
	{_id:'ObjectID("5359550868cc9b2b372e2ea8")', text:''},
	{_id:'ObjectID("5359550868cc9b2b372e2ea9")', text:''},
	{_id:'ObjectID("5359550868cc9b2b372e2e10")', text:''},
];

can.fixture({
  "GET /api/documents": function(){
    return documents;
  }
})