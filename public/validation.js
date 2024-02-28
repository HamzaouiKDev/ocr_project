
function myFunction()
{
var table=$('#sample_data').DataTable();
var table1= document.getElementById('sample-data');

table.cells().every(function(){

var cell=this;
var cellData=cell.data();

if(cellData=='EFA.01.19')

     {
        table.cells().every(function(){

            var cell=this;
            var cellData=cell.data();
            
            if(cellData=='EFA.01.19')
            
                 {
                  $(cell.node()).addClass('blink-background');
                 }
            
            });
      $(cell.node()).addClass('blink-background');
     }

});













//var cell = table.cell(':eq(1)',3).node();
//var cellData= table.cell(0,1).data();
//$(cell).addClass('blink-background');
//alert(cellData);
}