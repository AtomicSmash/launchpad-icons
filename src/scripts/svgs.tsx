import type { IconNames } from "./svgs.editor";
import type { SVGProps } from "react";
import { Suspense, lazy } from "react";

type IconProps = Omit<SVGProps<SVGSVGElement>, "ref"> & {
	iconName: IconNames;
	size?: string;
	isEditorMode?: boolean;
};

const ManifestedIcon = lazy(async () => {
	const assetManifest = await import(
		/* webpackIgnore: true */ `/wp-content/plugins/launchpad-icons/dist/assets-manifest.json?no_cache=${Date.now()}`,
		{ with: { type: "json" } }
	)
		.then((module: { default: Record<string, unknown> }) => {
			if (
				!module.default["icons/sprite.svg"] ||
				typeof module.default["icons/sprite.svg"] !== "string"
			) {
				throw new Error("Icon sprite missing from manifest.");
			}
			return module.default as Record<string, string>;
		})
		.catch(() => ({}) as Record<string, never>);
	return {
		default: function ManifestedIcon(props: IconProps) {
			const { iconName, size, isEditorMode = false, ...svgProps } = props;
			return (
				<svg
					xmlns="http://www.w3.org/2000/svg"
					width={size}
					height={size}
					{...svgProps}
				>
					{/* Full URL is required to make SVGs load in iframed block editor. */}
					<use
						href={`${isEditorMode ? `${window.location.protocol}//${window.location.host}` : ""}/wp-content/plugins/launchpad-icons/dist/${assetManifest["icons/sprite.svg"]}#${iconName}`}
					/>
				</svg>
			);
		},
	};
});

export function Icon(props: IconProps) {
	return (
		<Suspense fallback={null}>
			<ManifestedIcon {...props} />
		</Suspense>
	);
}
