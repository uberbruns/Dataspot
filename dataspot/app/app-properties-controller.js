App.CAFieldProperties = Ember.Resource.extend({

	resourceUrl: undefined,
	resourceName: 'property',
	resourceIdField: 'id',
	resourceProperties: [
		'id', 'libraryId', 'sectionId', 'displayName',
		'default', 'type', 'order',
		'label', 'insert', 'prepend', 'append', 'labelOrder',
		'name', 'autoName', 'index',
	],

	controller: undefined,

	'id' : undefined,
	'name' : 'name',
	'sectionId' : 0,
	'displayName' : 'Name',
	'default' : '',
	'type' : 'text',
	'order' : 99,
	'label' : undefined,
	'insert' : undefined,
	'prepend' : undefined,
	'append' : undefined,
	'labelOrder' : undefined,
	'autoName': 1,
	'index': 0,

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


})





App.PropertiesController = Ember.ResourceController.extend({

	resourceType: App.CAFieldProperties,
	inspectedField: undefined,
	library: undefined,
	selectedLibraryBinding: 'library.controller.selectedLibrary',


	_resourceUrl: function() {

		return this.get('library')._resourceUrl() + '/properties';

	},



	fieldProperties: function() {

		return this.get('content')

	}.property('content'),


	// fields: function() {

	// 	try { throw new Error("dummy"); } catch (e) { console.log(e.stack); }

	// 	return this.get('content')

	// }.property(),



	update: function(callback) {

		var self = this

		this.findAll().done(function() {

			self.get('content').setEach('resourceUrl', self._resourceUrl())
			self.get('content').setEach('controller', self)

			var sortedContent = self.get('content').sort(function(a,b) {
				return a.order-b.order;
			})
			self.set('content', sortedContent)
			if (callback) callback()


		}).fail(function(e) {

			console.log(e)

		})

	},


	propertiesWithId: function(id) {

		return _(this.get('fieldProperties')).find(function(property) { return (property.get('id') == id) })

	}

})


