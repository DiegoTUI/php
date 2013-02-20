 <?php
 $TICKETAVAILRS_EXAMPLE = '<!-- io:header name="X-Forwarded-Proto" value="http"/
  --> 
<?xml version="1.0" encoding="UTF-8" ?> 
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
soapenv:Body>
  <ns1:getTicketAvail xsi:type="xsd:string" xmlns:ns1="http://axis.frontend.hydra.hotelbeds.com">
  <TicketAvailRS 
  xmlns="http://www.hotelbeds.com/schemas/2005/06/messages" 
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
  xsi:schemaLocation="http://www.hotelbeds.com/schemas/2005/06/messages TicketAvailRS.xsd" 
  totalItems="19" 
  echoToken="DummyEchoToken">
  <AuditData>
  <ProcessTime>565</ProcessTime>
  <Timestamp>2013-02-20 12:00:00.629</Timestamp> <RequestHost>10.162.2.147</RequestHost> <ServerName>FORM</ServerName> <ServerId>FO</ServerId> <SchemaRelease>2005/06</SchemaRelease> <HydraCoreRelease>2.0.201211201654</HydraCoreRelease>
  <HydraEnumerationsRelease>1.0.201211201654</HydraEnumerationsRelease>
  <MerlinRelease>N/A</MerlinRelease>
  </AuditData>
  <PaginationData currentPage="1" totalPages="1"/>
  <ServiceTicket xsi:type="ServiceTicket" availToken="4KtryHlZPBvIvNXZk66ZQg==">
  <DateFrom date="20130420"/>
  <DateTo date="20130422"/>
  <Currency code="EUR">Euro</Currency>
  <TicketInfo xsi:type="ProductTicket">
  <Code>000200515</Code>
  <Name>PALMA AQUARIUM</Name>
  <DescriptionList>
  <Description type="generalDescription" languageCode="ENG">Discover the best kept secrets of the oceans as you walk around this wonderful Marine Park with over 8,000 animals representing 700 different species and more than 5 million litres of salt water. Follow the 900 metre itinerary through the park and discover its amazing secrets. You will enjoy a host of activities allowing you to learn about life in marine environments while you enjoy fun animations suitable for all ages, especially for kids. Take a plunge and go diving in the deepest shark aquarium in Europe and you&#8217;ll have the one of the most amazing experiences of your life. Palma Aquarium is the only tourist attraction in the Balearic Islands that is open all year round 365 days a year. During the summer months, the heat can sometimes be unbearable, and for this reason we have thought up loads of fun and refreshing activities for you to enjoy and cool down at the same time. You&#8217;ll really feel like you&#8217;re swimming in the sea as you dive among the playful manta rays and have a ball in the fountain jets in the playground or just cool down and enjoy the Jungle or the air-conditioned aquariums indoors. So when you start to feel the heat ... we&#8217;ll be waiting for you!</Description>
  </DescriptionList>
  <ImageList>
  <Image><Type>L</Type><Order>0</Order><VisualizationOrder>0</VisualizationOrder>
  <Url>http://www.hotelbeds.com/giata/extras/big/01353/01353_1.jpg</Url>
  </Image>
  <Image>
  <Type>S</Type><Order>0</Order><VisualizationOrder>0</VisualizationOrder>
  <Url>http://www.hotelbeds.com/giata/extras/small/01353/01353_1.jpg</Url>
  </Image></ImageList>
  <CompanyCode>E10</CompanyCode>
  <TicketClass>T</TicketClass>
  <Destination type="SIMPLE" code="PMI"></Destination>
  <TicketZone xsi:type="ProductZone"></TicketZone>
  </TicketInfo>
  <AvailableModality code="0#8">
  <Name>GENERAL ENTRANCE</Name>
  <Contract>
  <Name>AQUARIUM 13</Name>
  <IncomingOffice code="1"></IncomingOffice>
  </Contract>
  <PriceList>
  <Price><Amount>18.781</Amount><Description>ADULT PRICE</Description></Price><Price><Amount>14.200</Amount><Description>CHILD PRICE</Description></Price>
  <Price><Amount>0.000</Amount><Description>INFANT PRICE</Description></Price><Price><Amount>18.781</Amount><Description>SERVICE PRICE</Description></Price>
  <Price><Amount>22.500</Amount><Description>TICKET OFFICE PRICE</Description></Price><Price><Amount>15.500</Amount><Description>CHILD TICKET OFFICE PRICE</Description></Price>
  </PriceList>
  <Type code="D">Days</Type>
  <Mode code="P">Person</Mode>
  <OperationDateList>
  <OperationDate date="20130420" minimumDuration="1" maximumDuration="1"/><OperationDate date="20130421" minimumDuration="1" maximumDuration="1"/>
  <OperationDate date="20130422" minimumDuration="1" maximumDuration="1"/>
  </OperationDateList>
  <ChildAge ageFrom="4" ageTo="12"/>
  <ContentSequence>1353</ContentSequence>
  </AvailableModality>
  <Paxes><AdultCount>1</AdultCount><ChildCount>0</ChildCount></Paxes>
  </ServiceTicket>
  </TicketAvailRS>
  </ns1:getTicketAvail> 
  </soapenv:Body>
  </soapenv:Envelope>' 
  ?>