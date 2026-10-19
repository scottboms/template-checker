<template>
	<k-field
		:name="name"
		:label="label"
		class="k-thumbnail-field"
	>
		<k-item
			:image="{ src: src }"
			:text="filename"
			:info="size"
		/>
	</k-field>
</template>

<script>
export default {
	name: "ThumbnailField",
  props: {
		name: String,
		label: String,
    src: String,
    alt: String,
    filename: String,
		size: String
  }
};
</script>
