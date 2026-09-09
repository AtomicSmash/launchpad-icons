import type { IconNames } from "./svgs.editor";
import type { IconLibraries, IconMetaData } from "@launchpadBlocks/svgs.editor";
import domReady from "@wordpress/dom-ready";
import { addFilter } from "@wordpress/hooks";
import { __ } from "@wordpress/i18n";
import { Icon } from "./svgs";
import { iconMetaData } from "./svgs.editor";

// Add custom icons to the Icon Block.
domReady(() => {
	addFilter(
		"launchpadBlocks.icons",
		"launchpad/icons",
		(iconLibraries: IconLibraries) => {
			return {
				launchpad: {
					name: __("Launchpad", "launchpad"),
					renderIcon: (iconName, svgProps) => (
						<Icon iconName={iconName as IconNames} isEditorMode {...svgProps} />
					),
					availableIcons: Object.fromEntries(
						Object.entries(iconMetaData).filter(
							([_slug, icon]) =>
								(icon as IconMetaData[keyof IconMetaData])
									.makeAvailableToUser === true,
						),
					),
				},
				...iconLibraries,
			} satisfies IconLibraries;
		},
	);
});
