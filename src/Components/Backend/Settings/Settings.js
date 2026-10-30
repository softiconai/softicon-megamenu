import { InspectorControls } from '@wordpress/block-editor';
import { TabPanel } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';
import General from './General/General';
import Style from './Style/Style';
import { PanelGroup } from '../../../vendor/softicon-tools/components/Panels';
import { tabs } from '../../../utils/options';

const Settings = ({ attributes, setAttributes }) => {
	return <InspectorControls>
		<TabPanel className='siSettings' tabs={tabs}>
			{(tab) => <PanelGroup key={tab.name} initial={'style' === tab.name ? 'size' : 'panel'}>
				{'general' === tab.name && <General {...{ attributes, setAttributes }} />}

				{'style' === tab.name && <Style {...{ attributes, setAttributes }} />}

				{/* Add-ons append their own panels to either tab. */}
				{applyFilters('almm.block.inspector', null, { tab: tab.name, attributes, setAttributes })}
			</PanelGroup>}
		</TabPanel>
	</InspectorControls>;
};

export default Settings;
