App.CASection = Ember.Resource.extend({

	resourceUrl: undefined,
	resourceName: 'section',
	resourceIdField: 'id',
	resourceProperties: ['id', 'name', 'order'],
	controller: undefined,

	allFieldsBinding: 'controller.library.indexController.selectedIndexCard.recordController.fields',

	id: undefined,
	name: 'foo',
	order: 99,


	save: function(callback) {

		var self = this

		this.saveResource().done( function() {

			if (callback) callback(1)

		}).fail(function(e) {

			if (callback) callback(0)

		})

	},



	fields: function() {

		if (this.get('allFields')) {

			var allFields = this.get('allFields')
			var self = this

			return _(allFields).filter(function(field) {

				return (self.get('id') == field.get('properties').get('sectionId'))

			})

		} else {

			return []

		}

	}.property('id','allFields')


})





App.SectionsController = Ember.ResourceController.extend({

	resourceType: App.CASection,
	inspectedField: undefined,
	library: undefined,
	selectedLibraryBinding: 'library.controller.selectedLibrary',
	selectedSection: null,


	_resourceUrl: function() {

		return this.get('library')._resourceUrl() + '/sections';

	},


	sections: function() {

		return this.get('content')

	}.property('content'),


	update: function(callback) {

		var self = this

		this.findAll().done(function() {

			self.get('content').setEach('resourceUrl', self._resourceUrl())
			self.get('content').setEach('controller', self)
			if (callback) callback()

		}).fail(function() {

			if (callback) callback()

		})

	},

	newSection: function() {

		var self = this

		var newSection = App.CASection.create({
			'id' : undefined,
			'resourceUrl' : self._resourceUrl(),
			'name' : 'Unnamed Section',
			'order' : 9999,
		})

		newSection.saveResource().done(function() {

			self.update(function() {

				App.selected('recordController').update();

			})

		}).fail(function(e) {


		})

	} 

})



