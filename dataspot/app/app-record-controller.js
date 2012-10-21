App.CAField = Ember.Resource.extend({

	resourceUrl: undefined,
	resourceName: 'field',
	resourceProperties: ['id', 'recordId', 'propertyId', 'value'],

	id : undefined,
	value: '',
	valueHasChanged: false,
	controller: undefined,


	init: function() {


	},
	

	valueChanged: Ember.observer(function() {

		if (!this.get('valueHasChanged')) this.set('valueHasChanged', true)

	 }, 'value'),


	save: function() {

		var self = this

		if (this.get('valueHasChanged')) {

			this.saveResource().done( function(e) {

				if (self.get('label') && self.get('label') == 'title') {
					self.set('controller.indexCard.title', self.get('value'))
				}

			}).fail(function(e) {

			})

		}

	},

	properties: function() {

		var propertiesController = App.get('libraryController.selectedLibrary.propertiesController')
		var self = this;
		var properties = _(propertiesController.get('fieldProperties')).find(function(fieldProperties) {
			return (fieldProperties.id == self.propertyId)
		})

		return properties;

	}.property('id'),


	section: function() {

		var sectionsController = this.get('controller.indexCard.controller.library.sectionsController')
		var self = this;
		var section = _(sectionsController.get('sections')).find(function(aSection) {
			return (aSection.get('id') == self.get('properties.sectionId'))
		})

		return section

	}.property('sectionId')

})




App.RecordController = Ember.ResourceController.extend({

	resourceType: App.CAField,
	selectedField: undefined,
	editField: undefined,

	_resourceUrl: function() {

		var resourceUrl = this.get('indexCard')._resourceUrl() + '/fields'
		return resourceUrl;

	},


	fields: function() {

		return this.get('content')

	}.property('content'),


	update: function(callback) {

		var self = this

		this.findAll().done(function() {

			self.get('content').setEach('resourceUrl', self._resourceUrl())
			self.get('content').setEach('controller', self)

			var sortedContent = self.get('content').sort(function(a,b) {
			    return a.order-b.order
			})
			self.set('content', sortedContent)
			if (callback) callback()


		}).fail(function() {  })

	},


})




App.RecordView = Ember.View.extend({

	indexCardBinding: null,

	sectionsBinding: 'indexCard.controller.library.sectionsController.sections',

	didInsertElement : function() {

		App.panelDidInsert(this)

	},

	indexCardChanged: Ember.observer(function() {

		Ember.run.schedule('sync', this.$(), function(){

			$('.textarea-shadow').remove()

		})

	 }, 'indexCard'),


	addField : function() {

		var self = this;

		var newField = App.CAFieldProperties.create({
			'resourceUrl' : App.selected('library')._resourceUrl() + '/properties',
			'name' : 'unnamed',
			'displayName' : 'Unnamed',
			'sectionId' : App.selected('section').get('id'),
		})

		newField.saveResource().done( function(e) {

			App.selected('propertiesController').update(function() {

				App.selected('recordController').update()

			})

		}).fail(function(e) {


		})

	},

	deleteField: function() {

		App.selected('field').get('properties').destroyResource().done(function() {

			App.selected('propertiesController').update(function() {

				App.selected('recordController').update()

			})		

		}).fail(function(e) {});

	},

	addSection : function() {

		var sectionsController = App.selected('sectionsController')
		sectionsController.newSection()

	},

	deleteSection: function() {

		App.selected('section').destroyResource().done(function() {

			App.selected('sectionsController').update(function() {

				App.selected('propertiesController').update(function() {

					App.selected('recordController').update()

				})		

			})		

		}).fail(function(e) {

			console.log(e)

		});

	},

})




App.RecordFieldTextArea = Ember.TextArea.extend({

	didInsertElement: function() {

		Ember.run.schedule('timers', this.$(), function(){

			this.autogrow()

		})

	},


})



App.SectionTitle = Ember.View.extend({

	sectionBinding: null,
	classNames: ['section-title'],

	edit: function () {

		this.set('section.controller.selectedSection', this.get('section'))
		App.inspectorController.inspectSection();

	},

});


App.RecordField = Ember.View.extend({

	fieldBinding: null,
	classNames: ['field'],
	classNameBindings: ['isSelected', 'isInspected'],

	edit: function () {

		var recordController = this.get('field.controller');
		var sectionsController = this.get('field.controller.indexCard.controller.library.sectionsController')

		recordController.set('selectedField', this.get('field'))
		App.inspectorController.inspectProperties();

	},

	focusOut: function() {

		this.get('field').save()

	},

	focusIn: function() {

		this.edit()
		this.set('field.valueHasChanged', false)

	},

	isSelected: function() {

		var selectedField = this.get('field.controller.selectedField');
		return  (selectedField && selectedField.get('propertyId') == this.get('field.propertyId'))

	}.property('field.controller.selectedField','field'),

	isInspected: function() {

		return (this.get('isSelected') && App.get('inspectorController.activeInspector') == 'properties')

	}.property('isSelected','App.inspectorController.activeInspector')


});
