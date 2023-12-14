


$(document).ready(function(){

 $('#sample_data').DataTable({
  processing: true,
  serverSide: true,
  order:[],
  scrollX: true,
  paging:false,


  //ajax: "{{path('get_users')}}",
 
  ajax: {
    url: '/getpage',
    type: "POST",
    dataSrc: "data",
   
        },
        
  // These are the column name variables that will be sent to the server
columnDefs: [
    { "data": "id",   "targets": 0 },
    { "data": "code",   "targets": 1 },
    { "data": "label",  "targets": 2 },
    { "data": "notes",  "targets": 3 },
    { "data": "value_n",   "targets": 4 },
    { "data": "value_n1",   "targets": 5 },
    { "data": "type_page",  "targets": 6 },


],

  createdRow:function(row, data, rowIndex)
  {
    $.each($('td', row), function(colIndex){
      if(colIndex == 1)
      {
        $(this).attr('data-name', 'code');
        $(this).attr('class', 'code');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk', data['id']);
      }
      if(colIndex == 2)
      {
        $(this).attr('data-name', 'label');
        $(this).attr('class', 'label');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk', data['id']);
      }
      if(colIndex == 3)
      {
        $(this).attr('data-name', 'notes');
        $(this).attr('class', 'notes');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk',data['id']);
      }
      if(colIndex == 4)
      {
        $(this).attr('data-name', 'value_n');
        $(this).attr('class', 'value_n');
        $(this).attr('data-type','text');
        $(this).attr('data-pk', data['id']);
      }
      if(colIndex == 5)
      {
        $(this).attr('data-name', 'value_n1');
        $(this).attr('class', 'value_n1');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk', data['id']);
      }
      if(colIndex == 6)
      {
        $(this).attr('data-name', 'type_page');
        $(this).attr('class', 'type_page');
        $(this).attr('data-type', 'select');
        $(this).attr('data-pk',data['id']);
      }
    
    });
  }
});
$('#sample_data').editable({
  mode: 'inline',
  container:'body',
  selector:'td.code',
  url:'/updatecode',
  title:'Code',
  type:'POST',
  validate:function(value){
    if($.trim(value) == '')
    {
      return 'Ce champ est obligatoire ';
    }
  }
});

$('#sample_data').editable({
  mode: 'inline',
  container:'body',
  selector:'td.label',
  url:'/updatelabel',
  title:'Label',
  type:'POST',
  validate:function(value){
    if($.trim(value) == '')
    {
      return 'Ce champ est obligatoire ';
    }
  }
});

$('#sample_data').editable({
  mode: 'inline',
  container:'body',
  selector:'td.notes',
  url: '/updateNotes',
  title:'Notes',
  type:'POST',
 
});

$('#sample_data').editable({
  mode: 'inline',
  container:'body',
  selector:'td.value_n',
  url: '/updateAnneeN',
  title:'Annee N',
  type:'POST',
 
});

$('#sample_data').editable({
  mode: 'inline',
  container:'body',
  selector:'td.value_n1',
  url: '/updateAnneeN1',
  title:'Annee N-1',
  type:'POST',
 
});
$('#sample_data').editable({
  mode: 'inline',
  container:'body',
  selector:'td.type_page',
  url: '/updateTypePage',
  title:'Type de la page',
  type:'POST',
 
});
/*
$('#sample_data').editable({
  container:'body',
  selector:'td.gender',
  url:'/updateGender',
  title:'Sexe',
  type:'POST',
  datatype:'json',
  source:[{value: "Male", text: "Male"}, {value: "Female", text: "Female"}],
  validate:function(value){
    if($.trim(value) == '')
    {
      return 'Ce champ est obligatoire';
    }
  }
});*/

});
