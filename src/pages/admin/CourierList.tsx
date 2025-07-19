import { useState } from "react";
import { Layout } from "@/components/courier/Layout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Edit, Trash2, Eye, Search, Filter } from "lucide-react";

const CourierList = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [statusFilter, setStatusFilter] = useState("all");

  // Mock data
  const couriers = [
    {
      id: 1,
      trackingNumber: "CP12345678",
      senderName: "John Doe",
      receiverName: "Jane Smith",
      receiverCity: "New York",
      status: "in-transit",
      createdDate: "2024-01-15"
    },
    {
      id: 2,
      trackingNumber: "CP87654321",
      senderName: "Alice Johnson",
      receiverName: "Bob Wilson",
      receiverCity: "Los Angeles",
      status: "delivered",
      createdDate: "2024-01-14"
    },
    {
      id: 3,
      trackingNumber: "CP11223344",
      senderName: "Mike Brown",
      receiverName: "Sarah Davis",
      receiverCity: "Chicago",
      status: "pending",
      createdDate: "2024-01-16"
    },
    {
      id: 4,
      trackingNumber: "CP55667788",
      senderName: "Emma Wilson",
      receiverName: "David Lee",
      receiverCity: "Houston",
      status: "picked-up",
      createdDate: "2024-01-15"
    }
  ];

  const getStatusBadge = (status: string) => {
    const statusColors = {
      pending: "secondary" as const,
      "picked-up": "default" as const,
      "in-transit": "default" as const,
      delivered: "default" as const,
      cancelled: "destructive" as const
    };
    return <Badge variant={statusColors[status as keyof typeof statusColors] || "default"}>{status.replace("-", " ")}</Badge>;
  };

  const filteredCouriers = couriers.filter(courier => {
    const matchesSearch = courier.trackingNumber.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         courier.senderName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         courier.receiverName.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesStatus = statusFilter === "all" || courier.status === statusFilter;
    return matchesSearch && matchesStatus;
  });

  return (
    <Layout userRole="admin" userName="John Smith" userEmail="admin@courierpro.com">
      <div className="p-6">
        <div className="mb-6">
          <h1 className="text-2xl font-bold text-foreground">Courier Management</h1>
          <p className="text-muted-foreground">Manage all courier shipments</p>
        </div>

        <Card>
          <CardHeader>
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
              <CardTitle>All Couriers</CardTitle>
              <div className="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                <div className="relative">
                  <Search className="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                  <Input
                    placeholder="Search couriers..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="pl-10 w-64"
                  />
                </div>
                <Select value={statusFilter} onValueChange={setStatusFilter}>
                  <SelectTrigger className="w-40">
                    <Filter className="h-4 w-4 mr-2" />
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">All Status</SelectItem>
                    <SelectItem value="pending">Pending</SelectItem>
                    <SelectItem value="picked-up">Picked Up</SelectItem>
                    <SelectItem value="in-transit">In Transit</SelectItem>
                    <SelectItem value="delivered">Delivered</SelectItem>
                    <SelectItem value="cancelled">Cancelled</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div className="rounded-md border">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Tracking Number</TableHead>
                    <TableHead>Sender</TableHead>
                    <TableHead>Receiver</TableHead>
                    <TableHead>Destination</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Date</TableHead>
                    <TableHead className="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {filteredCouriers.map((courier) => (
                    <TableRow key={courier.id}>
                      <TableCell className="font-medium">{courier.trackingNumber}</TableCell>
                      <TableCell>{courier.senderName}</TableCell>
                      <TableCell>{courier.receiverName}</TableCell>
                      <TableCell>{courier.receiverCity}</TableCell>
                      <TableCell>{getStatusBadge(courier.status)}</TableCell>
                      <TableCell>{courier.createdDate}</TableCell>
                      <TableCell className="text-right">
                        <div className="flex justify-end space-x-2">
                          <Button variant="outline" size="sm">
                            <Eye className="h-4 w-4" />
                          </Button>
                          <Button variant="outline" size="sm">
                            <Edit className="h-4 w-4" />
                          </Button>
                          <Button variant="outline" size="sm">
                            <Trash2 className="h-4 w-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </div>
          </CardContent>
        </Card>
      </div>
    </Layout>
  );
};

export default CourierList;