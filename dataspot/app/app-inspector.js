
App.PropertiesInspector = Ember.View.extend({

	fieldBinding: 'App.libraryController.selectedLibrary.indexController.selectedIndexCard.recordController.selectedField',
	sectionBinding: 'App.libraryController.selectedLibrary.sectionsController.selectedSection',
	libraryBinding: 'App.libraryController.selectedLibrary',

	classNames: ['panel'],
	classNameBindings: ['App.libraryController.showInspectionPanel'],
	elementId: 'properties-inspector',
	inspectedViewBinding: 'App.inspectorController.activeInspector',

	close: function() {
		App.set('libraryController.showInspectionPanel', false)
	},

	labelOptions: Ember.A(['hidden','title', 'text','minimal','strong','highlighted']),
	labelTexts: Ember.A(['Hidden','Title', 'Text','Minimal','Strong','Highlighted']),

	insertOptions : Ember.A(['inline', 'newline','spacing','seperate']),
	insertTexts : Ember.A(['Inline', 'New Line','Spacing','Seperate']),

	typeOptions: Ember.A(['text', 'select','checkbox']),
	typeTexts: Ember.A(['Text', 'Select','Checkbox']),

	boolOptions: Ember.A([1,0]),
	boolTexts: Ember.A(['Yes', 'No']),

})



App.inspectorController = Ember.Object.create({

	activeInspector: 'library',

	inspectLibrary: function() {
		this.set('activeInspector', 'library')
	},

	inspectIndexCard: function() {
		// this.set('activeInspector', 'index')
	},

	inspectSection: function() {
		this.set('activeInspector', 'section')
	},

	inspectProperties: function() {
		this.set('activeInspector', 'properties')
	},

})



App.PropertiesInspectorTextInput = Ember.TextField.extend({

	fieldBinding: null,
	resourceBinding: null,

	focusOut: function() {
		this.save()
	},

	insertNewline: function() {
		this.save()
	},

	save: function() {

		if (this.get('resource') && this.get('resource').save) {
			this.get('resource').save()
		}

	}

})




App.SelectView = Ember.ContainerView.extend({

	tagName: "select",
	resourceBinding: undefined,
	valueBinding: undefined,
	
	optionsBinding: undefined,
	textsBinding: undefined,

	change: function(event) {

		var selectElement = $(event.target)
		var optionElement = selectElement.find("option:selected")
		var value = optionElement.attr('value')
		var self = this

		if (this.get('resource') && this.get('resource').save) {

			this.set('value', value)

			Ember.run.schedule('sync', this.$(), function(){

				self.get('resource').save()

			})

		}

	},

	childViews: function() {

		var optionsViews = Ember.A([])
		var options = this.get('options')
		var texts = this.get('texts')

		for (var i = 0; i < options.length; i++) {
			var anOption = options.objectAt(i)
			var aText = texts.objectAt(i)
			var option = Ember.View.create({
				tagName: 'option',
				attributeBindings: ['value'],
				value: anOption,
				template: Ember.Handlebars.compile(aText)
			})
			optionsViews.pushObject(option);
		};

		return optionsViews

	}.property('options'),


	didInsertElement : function() {

		this.$().val(this.get('value'))

	}

})




App.SelectSectionOrderView = Ember.ContainerView.extend({

	tagName: "select",
	resourceBinding: undefined,
	sectionBinding: 'resource',

	change: function(event) {

		var selectElement = $(event.target)
		var optionElement = selectElement.find("option:selected")
		var order = optionElement.attr('data-order')
		var self = this
		
		this.set('section.order', order)
		this.get('section').save(function() {

			self.get('section.controller').update(function() {

				App.selected('recordController').update(function() {
					selectElement.selectedIndex = 0;
				})

			})

		})

	},

	childViews: function() {

		var optionsViews = Ember.A([])
		var sections = this.get('section.controller.sections')

		var emptyOption = Ember.View.create({
			tagName: 'option',
			template: Ember.Handlebars.compile('Change Position')
		})

		optionsViews.pushObject(emptyOption);

		var order = 0
		var optionLabel = ''

		sections.forEach(function(section) {

			order = parseInt(section.get('order'))
			optionLabel = section.get('name')

			var option = Ember.View.create({
				tagName: 'option',
				attributeBindings: ['data-order', 'data-sectionId'],
				'data-order': order - 1,
				template: Ember.Handlebars.compile('Before ' + optionLabel)
			})

			optionsViews.pushObject(option);

		})

		var lastOption = Ember.View.create({
			tagName: 'option',
			attributeBindings: ['data-order', 'data-sectionId'],
			'data-order': order + 1,
			template: Ember.Handlebars.compile('Last Position')
		})
		
		optionsViews.pushObject(lastOption);

		return optionsViews;

	}.property(),




})


App.SelectFieldOrderView = Ember.ContainerView.extend({

	tagName: "select",
	resourceBinding: undefined,
	propertiesBinding: 'resource',

	change: function(event) {

		var selectElement = $(event.target)
		var optionElement = selectElement.find("option:selected")
		var order = optionElement.attr('data-order')
		var sectionId = optionElement.attr('data-sectionId')
		
		this.get('properties').set('order', order)
		this.get('properties').set('sectionId', sectionId)
		this.get('properties').save(function() {
			App.selected('recordController').update(function() {
				selectElement.selectedIndex = 0;
			})
		})

	},

	childViews: function() {

		var optGroupViews = Ember.A([]);
		var sections = this.get('properties').get('controller').get('library').get('sectionsController').get('sections')

		var emptyOption = Ember.View.create({
			tagName: 'option',
			template: Ember.Handlebars.compile('Change Position')
		})

		optGroupViews.pushObject(emptyOption);

		sections.forEach(function(section) {

			var optionGroup = Ember.ContainerView.create({

				tagName: 'optgroup',
				attributeBindings: ['label'],
				label: section.get('name'),

				childViews: function() {

					var optionsViews = Ember.A([]);
					var order = 0;
					var optionLabel = '';

					section.get('fields').forEach(function(field) {

						optionLabel = field.get('properties').get('displayName')
						order = parseInt(field.get('properties').get('order'))

						var option = Ember.View.create({
							tagName: 'option',
							attributeBindings: ['data-order', 'data-sectionId'],
							'data-order': order - 1,
							'data-sectionId': section.get('id'),
							template: Ember.Handlebars.compile('Before ' + optionLabel)
						})

						optionsViews.pushObject(option);

					})

					var lastOption = Ember.View.create({
						tagName: 'option',
						attributeBindings: ['data-order', 'data-sectionId'],
						'data-order': order + 1,
						'data-sectionId': section.get('id'),
						template: Ember.Handlebars.compile('Last Position')
					})
					
					optionsViews.pushObject(lastOption);

					return optionsViews;

				}.property()

			})

			optGroupViews.pushObject(optionGroup);

		})

		return optGroupViews;

	}.property(),

})

