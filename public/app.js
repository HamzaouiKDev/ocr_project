


$(document).ready(function(){
//////////////////////////////Ajout d'une ligne/////////////////////////////////////////////////////////////////////////
  $('#btnAjouter').click(function() {
    $.ajax({
      url: '/ajoutLigne', 
      type: 'POST',
      success: function(response) {
          alert('Ligne ajoutée avec success !');
      },
      error: function(xhr, status, error) {
          console.log(error);
      }
  });
});

/*$('#addForm').submit(function(e) {
    e.preventDefault();
    $.ajax({
        url: '{{ path('add_new_line_route') }}', // Route Symfony pour l'ajout de la nouvelle ligne
        method: 'POST',
        data: $(this).serialize(), // Envoyer les données du formulaire
        success: function(response) {
            // Rafraîchir votre Datatable après l'ajout de la nouvelle ligne
            $('#datatable').DataTable().ajax.reload();
            $('#modalForm').hide(); // Cacher le formulaire modal
        },
        error: function(xhr, status, error) {
            console.log(error);
        }
    });
});*/
////////////////////////////////////////////////////////////////////////////////////////////
 var table= $('#sample_data').DataTable({
  order: [], // Désactiver l'ordre initial
  rowReorder: {
      selector: 'td:first-child' // Utiliser les lignes comme éléments de réorganisation
  },
  processing: true,
  serverSide: true,
  order:[],
  scrollX: true,
  paging:false,
  ordering: false,
  searching: false,
  info: false,
  select: true,
  language: { // Personnaliser les textes et les messages
   
    zeroRecords: 'Aucun enregistrement trouvé',
    infoEmpty: 'Aucun enregistrement disponible',
    emptyTable:'rien',
    
    },
  
  ajax: {
    url: '/getpage',
    type: "POST",
    dataSrc: "data",
   
        },
        
  // These are the column name variables that will be sent to the server
columnDefs: [
    { "data": "id",   "targets": 0 },
    { "data": "code", "targets": 1 },
    { "data": "label",  "targets": 2 },
    { "data": "notes",  "targets": 3 },
    { "data": "value_n",   "targets": 4 },
    { "data": "value_n1",   "targets": 5 },
   


],

  createdRow:function(row, data, rowIndex)
  {
    $.each($('td', row), function(colIndex){
      if(colIndex == 1)
      {
        $(this).attr('data-name', 'code');
        $(this).attr('class', 'code');
        $(this).attr('data-type', 'select');
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
     /* if(colIndex == 6)
      {
        $(this).attr('data-name', 'type_page');
        $(this).attr('class', 'type_page');
        $(this).attr('data-type', 'select');
        $(this).attr('data-pk',data['id']);
      }*/
    
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
  datatype: 'json',
  source: '/api/coders', 

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
/*$('#sample_data').editable({
  mode: 'inline',
  container:'body',
  selector:'td.type_page',
  url: '/updateTypePage',
  title:'Type de la page',
  type:'POST',
  source:[{value: "BILAN", text: "BILAN"}, {value: "ETAT DE RSULTAT", text: "ETAT DE RESULTAT"}],
 
});*/
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

table.on('row-reorder.dt', function (e, diff, edit) {
  // Affichez le bouton d'enregistrement des modifications
  $('#saveButton').show();
});
$('#saveButton').on('click', function () {
  // Mettez à jour l'ordre dans le tableau de données sans persister dans la base de données
  table.rows().data().each(function (data, index) {
      var rowId = $(table.row(index).node()).data('id');
      // Ici, vous pouvez effectuer une action comme mettre à jour l'ordre dans un tableau JavaScript temporaire
      // Par exemple : tempArray.push({ id: rowId, order: index });
  });
  // Une fois les changements confirmés, masquez le bouton d'enregistrement des modifications
  $(this).hide();
});
});

