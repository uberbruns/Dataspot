
App.CALibrary = Ember.Resource.extend({

	resourceUrl: App.defaults.get('baseResourceUrl') + '/libraries',
	resourceName: 'library',
	resourceProperties: ['id', 'name','displayName'],

	id: undefined,
	name: '',
	displayName: '',
	autoName: true,
	
	controller: null,
	indexController: null,
	propertiesController: null,
	sectionsController: null,

	prettyName: function() {

		return this.get('displayName')

	}.property('displayName'),


	init: function() {

		this.set('indexController', App.IndexCardController.create({ library: this }));
		this.set('propertiesController', App.PropertiesController.create({ library: this }));
		this.set('sectionsController', App.SectionsController.create({ library: this }));
		
	},


	save: function(callback) {

		var self = this

		this.saveResource().done( function() {

			if (callback) callback(1)

		}).fail(function(e) {

			if (callback) callback(0)

		})

	},
	

	displayNameChanged: Ember.observer(function() {

		if (this.get('autoName') == true) {

			this.set('name', _(this.get('displayName').toLowerCase()).camelize())

		}

	}, 'displayName'),



});




App.libraryController = Ember.ResourceController.create({

	resourceType: App.CALibrary,
	selectedLibrary: undefined,
	showInspectionPanel: false,


	libraries: function() {

		var sortedContent = Ember.copy(this.get('content')).sort(function(a,b) {
			return a.get('displayName').localeCompare(b.get('displayName'));
		})

		return sortedContent

	}.property('content','content.@each.displayName'),


	update: function() {

		var self = this;

		this.findAll().done(function() {

			self.get('content').setEach('controller', self)

			// Select First Library
			if (self.get('content').length) self.set('selectedLibrary', self.get('content')[0])

		}).fail(function() { });		

	},


	selectedLibraryChanged: Ember.observer(function() {

		var self = this;

		App.selected('propertiesController').update(function() {

			App.selected('sectionsController').update(function() {

				App.selected('indexController').update()

			})

		})


	 }, 'selectedLibrary'),


});




App.LibraryListView = Ember.View.extend({

	librariesBinding: 'App.libraryController.libraries',

	add: function() {

		var newLibraryName = prompt('Please Enter a Name:')

		if (newLibraryName) {

			var newLibrary = App.CALibrary.create({
				id: undefined,
				displayName: newLibraryName
			});

			newLibrary.saveResource().fail( function(e) {

				console.log(e)

			}).done(function() {

				App.libraryController.update()

			});

		}


	},


	delete: function() {
		
		App.libraryController.get('selectedLibrary').destroyResource().fail( function(e) {

		}).done(function() {

			App.libraryController.update()				

		});


	},



	didInsertElement : function() {

		App.panelDidInsert(this)

	}


})




App.LibraryListItemView = Ember.View.extend({

	libraryBinding: null,
	classNameBindings: ['isSelected'],
	classNames: ['item'],


	title: function() {

		return this.get('library').get('prettyName')

	}.property('library.prettyName', 'isSelected'),


	click: function() {

		App.selected('indexCard').set('content', Em.A([]));

		App.libraryController.set('selectedLibrary', this.get('library'))		
		App.inspectorController.inspectLibrary();

	},

	isSelected: function() {

		return (this.get('library') == App.libraryController.get('selectedLibrary'))

	}.property('App.libraryController.selectedLibrary'),



})
