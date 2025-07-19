import { useState } from "react";
import { Layout } from "@/components/courier/Layout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Plus, Edit, Trash2, UserCheck } from "lucide-react";

const AgentManagement = () => {
  const [newAgent, setNewAgent] = useState({
    name: "",
    email: "",
    phone: "",
    branch: ""
  });
  
  const [isDialogOpen, setIsDialogOpen] = useState(false);

  // Mock data
  const agents = [
    {
      id: 1,
      name: "Michael Johnson",
      email: "michael@courierpro.com",
      phone: "+1-555-0123",
      branch: "Downtown",
      status: "active",
      joinDate: "2024-01-10"
    },
    {
      id: 2,
      name: "Sarah Williams",
      email: "sarah@courierpro.com",
      phone: "+1-555-0124",
      branch: "Uptown",
      status: "active",
      joinDate: "2024-01-08"
    },
    {
      id: 3,
      name: "James Brown",
      email: "james@courierpro.com",
      phone: "+1-555-0125",
      branch: "Suburbs",
      status: "inactive",
      joinDate: "2024-01-05"
    }
  ];

  const handleAddAgent = (e: React.FormEvent) => {
    e.preventDefault();
    // Add agent logic here
    setIsDialogOpen(false);
    setNewAgent({
      name: "",
      email: "",
      phone: "",
      branch: ""
    });
  };

  const getStatusBadge = (status: string) => {
    return (
      <Badge variant={status === "active" ? "default" : "secondary"}>
        {status}
      </Badge>
    );
  };

  return (
    <Layout userRole="admin" userName="John Smith" userEmail="admin@courierpro.com">
      <div className="p-6">
        <div className="mb-6">
          <h1 className="text-2xl font-bold text-foreground">Agent Management</h1>
          <p className="text-muted-foreground">Manage courier agents and staff</p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center">
                <UserCheck className="h-8 w-8 text-primary" />
                <div className="ml-4">
                  <p className="text-sm font-medium text-muted-foreground">Total Agents</p>
                  <p className="text-2xl font-bold">12</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center">
                <UserCheck className="h-8 w-8 text-green-600" />
                <div className="ml-4">
                  <p className="text-sm font-medium text-muted-foreground">Active</p>
                  <p className="text-2xl font-bold">10</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center">
                <UserCheck className="h-8 w-8 text-yellow-600" />
                <div className="ml-4">
                  <p className="text-sm font-medium text-muted-foreground">Inactive</p>
                  <p className="text-2xl font-bold">2</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center">
                <UserCheck className="h-8 w-8 text-blue-600" />
                <div className="ml-4">
                  <p className="text-sm font-medium text-muted-foreground">Branches</p>
                  <p className="text-2xl font-bold">5</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardHeader>
            <div className="flex justify-between items-center">
              <CardTitle>All Agents</CardTitle>
              <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
                <DialogTrigger asChild>
                  <Button>
                    <Plus className="h-4 w-4 mr-2" />
                    Add Agent
                  </Button>
                </DialogTrigger>
                <DialogContent>
                  <DialogHeader>
                    <DialogTitle>Add New Agent</DialogTitle>
                  </DialogHeader>
                  <form onSubmit={handleAddAgent} className="space-y-4">
                    <div>
                      <Label htmlFor="name">Full Name</Label>
                      <Input
                        id="name"
                        value={newAgent.name}
                        onChange={(e) => setNewAgent(prev => ({ ...prev, name: e.target.value }))}
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="email">Email</Label>
                      <Input
                        id="email"
                        type="email"
                        value={newAgent.email}
                        onChange={(e) => setNewAgent(prev => ({ ...prev, email: e.target.value }))}
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="phone">Phone</Label>
                      <Input
                        id="phone"
                        type="tel"
                        value={newAgent.phone}
                        onChange={(e) => setNewAgent(prev => ({ ...prev, phone: e.target.value }))}
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="branch">Branch</Label>
                      <Input
                        id="branch"
                        value={newAgent.branch}
                        onChange={(e) => setNewAgent(prev => ({ ...prev, branch: e.target.value }))}
                        required
                      />
                    </div>
                    <div className="flex justify-end space-x-2">
                      <Button type="button" variant="outline" onClick={() => setIsDialogOpen(false)}>
                        Cancel
                      </Button>
                      <Button type="submit">Add Agent</Button>
                    </div>
                  </form>
                </DialogContent>
              </Dialog>
            </div>
          </CardHeader>
          <CardContent>
            <div className="rounded-md border">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Phone</TableHead>
                    <TableHead>Branch</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Join Date</TableHead>
                    <TableHead className="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {agents.map((agent) => (
                    <TableRow key={agent.id}>
                      <TableCell className="font-medium">{agent.name}</TableCell>
                      <TableCell>{agent.email}</TableCell>
                      <TableCell>{agent.phone}</TableCell>
                      <TableCell>{agent.branch}</TableCell>
                      <TableCell>{getStatusBadge(agent.status)}</TableCell>
                      <TableCell>{agent.joinDate}</TableCell>
                      <TableCell className="text-right">
                        <div className="flex justify-end space-x-2">
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

export default AgentManagement;