import React, { useState } from 'react';
import AgendaItem from '../AgendaItem';

export default function Agenda() {
	

	return (
		<>
		{items.map(item => (
			<AgendaItem title={item.title} startDate={item.startDate} description={item.description}></AgendaItem>
		))}
		</>
	)
}