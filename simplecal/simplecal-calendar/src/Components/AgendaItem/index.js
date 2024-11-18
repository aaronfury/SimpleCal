import {
    Card,
    CardHeader,
    CardBody,
    CardFooter,
    __experimentalText as Text,
    __experimentalHeading as Heading,
} from '@wordpress/components';
import DateObject from "react-date-object";

export default function AgendaItem(props) {
	var startDate = new DateObject(props.startDate * 1000);
	var endDate = new DateObject(props.endDate * 1000);

	return (
		<Card>
			<CardHeader>
				<Heading level={2}>{props.title}</Heading>
				<Heading level={3}>{startDate.format('MMMM DD, YYYY')}{startDate.format('MM/DD/YY') != endDate.format('MM/DD/YY') ? ' - ' + endDate.format('MMMM DD, YYYY') : null}</Heading>
			</CardHeader>
			<CardBody>
				<Text>{props.description}</Text>
			</CardBody>
			<CardFooter>
			</CardFooter>
		</Card>
	)
}