import type { IconNames } from "./svgs.editor";
import type { SVGProps } from "react";
type IconProps = Omit<SVGProps<SVGSVGElement>, "ref"> & {
    iconName: IconNames;
    size?: string;
    isEditorMode?: boolean;
};
export declare function Icon(props: IconProps): import("react/jsx-runtime").JSX.Element;
export {};
//# sourceMappingURL=svgs.d.ts.map