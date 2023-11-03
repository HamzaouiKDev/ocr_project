


$(document).ready(function(){
var dataTable = $('#sample_data').DataTable({
  processing: true,
  serverSide: true,
  order:[],
  scroll: true,
  ajax: {
    url: '/getuser',
    type: "POST",
    dataSrc: "data"
 
        },
  // These are the column name variables that will be sent to the server
columnDefs: [
    { "data": "id",   "targets": 0 },
    { "data": "path",   "targets": 1 },
    { "data": "matricule",  "targets": 2 },
    { "data": "annee",  "targets": 3 },
    { "data": "page",   "targets": 4 },
    { "data": "ligne",   "targets": 5 },
    { "data": "label",  "targets": 6 },
    { "data": "notes",  "targets": 7 },
    { "data": "value_n",   "targets": 8 },
    { "data": "value_n1",   "targets": 9 },
    { "data": "code",  "targets": 10 },
    { "data": "type_page",  "targets": 11 },


],
  createdRow:function(row, data, rowIndex)
  {
    $.each($('td', row), function(colIndex){
      if(colIndex == 1)
      {
        $(this).attr('data-name', 'path');
        $(this).attr('class', 'path');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk', data['id']);
      }
      if(colIndex == 2)
      {
        $(this).attr('data-name', 'matricule');
        $(this).attr('class', 'matricule');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk',data['id']);
      }
      if(colIndex == 3)
      {
        $(this).attr('data-name', 'annee');
        $(this).attr('class', 'annee');
        $(this).attr('data-type','text');
        $(this).attr('data-pk', data['id']);
      }
      if(colIndex == 4)
      {
        $(this).attr('data-name', 'page');
        $(this).attr('class', 'page');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk', data['id']);
      }
      if(colIndex == 5)
      {
        $(this).attr('data-name', 'ligne');
        $(this).attr('class', 'ligne');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk',data['id']);
      }
      if(colIndex == 6)
      {
        $(this).attr('data-name', 'label');
        $(this).attr('class', 'label');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk', data['id']);
      }
      if(colIndex == 7)
      {
        $(this).attr('data-name', 'notes');
        $(this).attr('class', 'notes');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk', data['id']);
      }
      if(colIndex == 8)
      {
        $(this).attr('data-name', 'value_n');
        $(this).attr('class', 'value_n');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk',data['id']);
      }
      if(colIndex == 9)
      {
        $(this).attr('data-name', 'value_n1');
        $(this).attr('class', 'value_n1');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk',data['id']);
      }
      if(colIndex == 10)
      {
        $(this).attr('data-name', 'code');
        $(this).attr('class', 'code');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk',data['id']);
      }
      if(colIndex == 11)
      {
        $(this).attr('data-name', 'type_page');
        $(this).attr('class', 'type_page');
        $(this).attr('data-type', 'text');
        $(this).attr('data-pk',data['id']);
      }
    });
  }
});

$('#sample_data').editable({
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

/*$('#sample_data').editable({
  container:'body',
  selector:'td.last_name',
  url: '/updatelastname',
  title:'Prénom',
  type:'POST',
  validate:function(value){
    if($.trim(value) == '')
    {
      return 'Ce champ est obligatoire';
    }
  }
});

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