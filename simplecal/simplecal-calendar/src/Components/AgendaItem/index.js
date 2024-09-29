import {
    Card,
    CardHeader,
    CardBody,
    CardFooter,
    __experimentalText as Text,
    __experimentalHeading as Heading,
} from '@wordpress/components';

export default function AgendaItem(props) {
	return (
		<Card>
			<CardHeader>
				<Heading level={2}>{props.title}</Heading>
			</CardHeader>
			<CardBody>
				<Text>{props.description}</Text>
			</CardBody>
			<CardFooter>
				<Text>{props.startDate}</Text>
			</CardFooter>
		</Card>
	)
}