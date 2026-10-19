<template>
	<div>
		<k-panel-inside>
			<k-view class="k-images-without-template-view">
				<k-header>Images</k-header>
				<k-section label="Images Without a File Blueprint">
					<k-items
						v-if="images.length"
						:items="items"
						:layout="table"
						:sortable="true"
					/>
					<k-grid v-else style="--columns: 1; gap: 0.5rem">
						<k-empty text='No images found' icon="image" />
					</k-grid>
				</k-section>
			</k-view>
		</k-panel-inside>
	</div>
</template>

<script>
export default {
	name: 'ImageList',
	props: {
		images: {
			type: Array,
			default: () => []
		},
		availableTemplates: {
			type: Array,
			default: () => []
		}
	},

	// data() {
// 		return {
// 			selectedImage: null,
// 			templateDrawer: false, // DEBUG, set to true
// 		};
// 	},

	// watch: {
// 		templateDrawer(open) {
// 			// console.log('templateDrawer changed:', open);
// 		}
// 	},


	computed: {
		items() {

			return this.images.map((image) => ({
				text: image.filename,
				info: image.parent,
				link: image.fileUrl,
				image: {
					src: image.thumbUrl,
					cover: true,
					back: 'pattern',
				},
				options: [
					image.fileUrl && 
					{
						icon: 'settings',
						text: 'Assign Template…',
						click: () => this.openTemplateSelector(image)
					},
					{
						icon: 'open',
						text: 'Edit Image...',
						link: image.fileUrl,
					},
					{
						icon: 'window',
						text: 'Preview',
						link: image.url
					}
				].filter(Boolean)
			}));
		}
	},

	methods: {
		handleTemplateSubmit(formData) {
		  const { key, template, alt } = formData;

		  this.$api.post("checker/assign", {
		    key,
		    template,
		    alt
		  })
		  .then(() => {
		    this.$panel.drawer.close();

		    this.$panel.notification.success({
		      message: "Changes saved",
		      timeout: 4000
		    });

		    this.$panel.view.reload();
		  })
		  .catch((error) => {
		    console.error("Template assignment failed:", error);

		    this.$panel.notification.error({
		      message: "An error occurred",
		      timeout: 4000
		    });
		  });
		},

		openTemplateSelector(image) {
			// console.log('Opening Drawer with File ID:', image.id);
			this.$panel.drawer.open({
				component: "k-form-drawer",

				props: {
					title: "Quick Edit",
					icon: "image",

					fields: {
						preview: {
							label: "Preview",
							type: "thumbnail",
							width: "1/1",
							src: image.thumbUrl,
							alt: image.alt || "",
							size: image.size,
							filename: image.filename
						},

						...(this.availableTemplates.length === 0
							? {
								notice: {
									label: "Notice",
									type: "info",
									icon: "info",
									theme: "warning",
									text: "No file blueprints are available. You need to create one first."
								}
							}
						: {
							template: {
								label: "Template",
								type: "select",
								required: true,
								options: this.availableTemplates,
								width: "1/1",
								help: "Choose an available file template"
							}
						}),

						alt: {
							label: "Alt Text",
							type: "textarea",
							buttons: false,
							size: "medium",
							width: "1/1"
						}
					},

					value: {
						key: image.id,
						template: image.templateInContent || null,
						alt: image.alt || ""
					}
				},

				on: {
					submit: (values) => this.handleTemplateSubmit(values)
				}
			});
		},

		closeDrawer() {
			this.$panel.drawer.close();
		}
	},

	mounted() {
		// debugging
		// console.log('Image Props:', this.images);
		// console.log('Available Templates:', this.availableTemplates);
	}
};
</script>
